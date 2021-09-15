<?php
namespace Opravdin\AmoHook;

abstract class Entities {
    /**
     * Любая сущность  
     * Обработчик выполнится вне зависимости от пришедшей сущности
     */
    const ANY = 'any';

    /**
     * Сделка
     */
    const LEAD = 'leads';

    /**
     * Контакт  
     * Не включает компании
     * @see COMPANY
     */
    const CONTACT = 'contacts';

    /**
     * Компания  
     * В хуке изначально приходит тип contacts, но библиотека при необходимости
     * преобразует его в компанию исходя из содержимого
     */
    const COMPANY = 'companies';

    /**
     * Задача
     */
    const TASK = "task";

    /**
     * Покупатель
     */
    const CUSTOMER = 'customers';

    /**
     * Каталог (список)
     * Включая счета, юр.лица и товары
     */
    const CATALOG = 'catalogs';

    /**
     * Неразобранное
     */
    const UNSORTED = 'unsorted';

    /**
     * Беседа
     */
    const TALK = 'talk';

    /**
     * Входящее сообщение
     */
    const MESSAGE = 'message';
}