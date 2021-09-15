<?php
namespace Opravdin\AmoHook;

abstract class Events {
    /**
     * Универсальное событие  
     * Обработчик выполнится вне зависимости от события
     */
    const ANY = 'any';

    /**
     * Изменение сущности
     */
    const UPDATE = 'update';

    /**
     * Удаление сущности
     */
    const DELETE = 'delete';

    /**
     * Примечание к сущности
     */
    const NOTE = 'note'; 

    /**
     * Добавление сущности
     */
    const ADD = 'add';

    /**
     * Смена статуса сделки
     */
    const STATUS = 'status';

    /**
     * Смена ответственного
     */
    const RESPONSIBLE = 'responsible';

    /**
     * Восстановление сущности
     */
    const RESTORE = 'restore';
}