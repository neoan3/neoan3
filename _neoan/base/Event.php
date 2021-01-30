<?php


namespace Neoan3\Core;


/**
 * Class Event
 * @package Neoan3\Core
 */
class Event
{
    /**
     * @var array
     */
    private static array $registeredEvents = [];
    /**
     * @var array
     */
    private static array $firedEvents = [];
    /**
     * @var null
     */
    private static $globalListener = null;

    /**
     * @param $eventName
     * @param $callback
     */
    public static function listen(string $eventName, $callback)
    {
        self::$registeredEvents[$eventName][] = $callback;
    }

    /**
     * @param $callback
     */
    public static function listenAll($callback)
    {
        self::$globalListener = $callback;
    }

    /**
     * @return array
     */
    public static function getRegisteredListeners(): array
    {
        return self::$registeredEvents;
    }

    /**
     * @return array
     */
    public static function getFiredEvents(): array
    {
        return self::$firedEvents;
    }

    /**
     * @param $eventName
     * @param array|string $params
     */
    public static function dispatch(string $eventName, $params = [])
    {
        self::$firedEvents[] = ['name'=>$eventName, 'params' => $params];
        if (self::$globalListener) {
            call_user_func(self::$globalListener, ['params' => $params, 'event' => $eventName]);
        }
        if (isset(self::$registeredEvents[$eventName])) {
            foreach (self::$registeredEvents[$eventName] as $registeredEventName => $callback) {
                if (!empty($params)) {
                    call_user_func($callback, $params);

                } else {
                    call_user_func($callback);
                }
            }
        }

    }
    public static function hook($eventName, $callback)
    {
        foreach (self::$firedEvents as $firedEvent){
            if($firedEvent['name'] == $eventName){
                call_user_func($callback, $firedEvent['params']);
            }
        }
    }
}