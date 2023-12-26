<?php

namespace App\Application\Common;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ObjectPool
{
    private array $available = [];
    private array $inUse = [];

    public $createNewDelegate = null;
    public $createParams = null;

    private LoggerInterface $logger;

    public ?string $poolName = null;
    public ?int $max = 0;
    public ?bool $logDisable = false;

    public function __construct()
    {
//        if (!is_null($container)) {
//            $this->logger = $container->get(LoggerInterface::class);
//        }
    }

    public function setAvailable(array $array): void
    {
        for($i=0; $i<count($array); $i++) {
            $this->available[] = $array[$i];
        }
    }

    public function echo(string $message): void
    {
        $now = date("Y-m-d H:i:s:u");
        echo "[{$now}]".$message.PHP_EOL;
    }

    public function printSizeViaLogger(?string $label) : void
    {
        if ($this->logDisable === false ) {
            $poolNameLabel = (is_null($this->poolName)? "" : $this->poolName)."-";
            if (!is_null($label)) {
                $this->echo("===".
                    $poolNameLabel."-".
                    "{$label}================================");
            }
            $this->echo ("===available size : ". count($this->available));
            $this->echo ("===inUse size : ". count($this->inUse));

            if (!is_null($label)) {
                $this->echo ("===========================================");
            }

            if ($this->max < count($this->inUse)) {
                $this->max = count($this->inUse);
            }
            $c = count($this->inUse);
            $this->echo("==={$poolNameLabel} max inuse Size : {$this->max} / current : {$c}");
        }
    }

    public function get(...$createParams)
    {
        $this->printSizeViaLogger("ObjectPool->get start");
        $createNew = $this->createNewDelegate;

        if (count($this->available) === 0) {
            $object = $createNew($createParams);
        } else {
            $object = array_pop($this->available);
        }

        $this->inUse[spl_object_hash($object)] = $object;
        $this->printSizeViaLogger("ObjectPool->get end");
        return $object;
    }

    public function dispose($object): void
    {
        $this->printSizeViaLogger("ObjectPool-dispose started");
        $key = spl_object_hash($object);

        if(isset($this->inUse[$key])) {
            unset($this->inUse[$key]);
            $this->available[] = $object;
        }
        $this->printSizeViaLogger("ObjectPool-dispose End");
    }
}