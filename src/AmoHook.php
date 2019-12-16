<?php
namespace Opravdin;

/**
 * AmoCRM Webhook processor class
 */
class AmoHook {
    /**
     * @var array Callbacks from methods
     * Format: $callbacks[entity][action] = [callback, callback...]
     */
    protected $callbacks = [];

    /**
     * @var mixed Raw AmoHook body
     */
    protected $rawBody = [];

    /**
     * @var array Pretty data
     */
    protected $prettyData = [];

    /**
     * @var function Error handler
     */
    protected $errorHandlers = [];

    /**
     * @var function After callback
     */
    protected $afterHandlers = [];
    
    /**
     * Construct instance from data
     */
    public static function build(array $requestBody) {
        return new AmoHook($requestBody);
    }

    /**
     * Construct instance from object data. Note that object should be array-castable
     */
    public static function fromObject(object $requestBody) {
        return new AmoHook((array)$requestBody);
    }

    /**
     * Create processor instance with webhook body
     */
    public function __construct(array $requestBody) {
        $this->rawBody = $requestBody;
    }

    /**
     * Get processed data without executing any callbacks
     */
    public function get() : array {
        if (count($this->prettyData))  {
            return $this->prettyData;
        }
        $pdata = [];
        foreach ($this->rawBody as $entityCategory=>$events) {
            if ($entityCategory === "account") {
                continue;
            }
            // Decoding JSON if needed
            if (is_string($events)) {
                $events = json_decode($events, true);
            }
            foreach ($events as $eventName=>$events) {
                foreach ($events as $event) {
                    $ename = $entityCategory;
                    if ($entityCategory === "contacts" && $event['type'] === "company") {
                        $ename = "companies";
                    }
                    $pdata[] = [
                        'entity' => $ename,
                        'action' => $eventName,
                        'data' => $event
                    ];
                }
            }
        }
        $this->prettyData = $pdata;
        return $pdata;
    }

    /**
     * Handle webhook chain
     */
    public function handle(){
        $data = $this->get();
        foreach ($data as $event) {
            $this->executeCallback($event['entity'], $event['action'], $event);
        }
    }

    /**
     * Register event callback
     */
    public function register($entities, $actions, $callback) : AmoHook {
        // Creating arrays from single strings
        if (is_string($entities)) {
            $entities = [$entities];
        }
        if (is_string($actions)) {
            $actions = [$actions];
        }

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $this->callbacks[$entity][$action][] = $callback;
            }
        }
        return $this;
    }

    /**
     * Register error callback
     */
    public function onError($callback) : AmoHook {
        $this->errorHandlers[] = $callback;
        return $this;
    }

    /**
     * Register after callback
     */
    public function after($callback): AmoHook {
        $this->afterHandlers[] = $callback;
        return $this;
    }

    /**
     * Execute callback function
     */
    private function executeCallback($entity, $action, $data){
        $e = [$entity, 'any'];
        $a = [$action, 'any'];
        $isCritical = false;
        foreach ($e as $entity) {
            foreach ($a as $action) {
                if (!isset($this->callbacks[$entity][$action])) {
                    continue;
                }
                foreach ($this->callbacks[$entity][$action] as $callback) {  
                    $result = null;
                    try {
                        $result = $callback($data);
                    } catch(\Throwable $e) {
                        if (count($this->errorHandlers) > 0) {
                            foreach ($this->errorHandlers as $h) {
                                $isCritical = $h($e, $data, $entity, $action) || $isCritical;
                            }
                        }
                    }     
                    if (count($this->afterHandlers) > 0) {
                        foreach ($this->afterHandlers as $h) {
                            $h($result, $data);
                        }
                    }
                    if ($isCritical === true) {
                        return;
                    }
                }
            }
        }
        return;        
    }
}