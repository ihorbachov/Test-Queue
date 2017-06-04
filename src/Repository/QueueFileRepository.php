<?php

namespace Repository;

use Collection\Queue;
use Entity\Message;

/**
 * Class QueueFileRepository
 * @package Repository
 */
class QueueFileRepository
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var Resource|null
     */
    private $storage;

    /**
     * QueueFileRepository constructor.
     * @param $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param Queue $queue
     * @return Queue
     */
    public function get(Queue $queue) : Queue
    {
        $this->startTransaction('r');

        while ($row = fgetcsv($this->storage)) {
            $message = new Message($row[0], $row[1]);
            $queue->enqueue($message);
        }

        $this->finishTransaction();
        return $queue;
    }

    /**
     * @param Queue $queue
     */
    public function put(Queue $queue)
    {
        $queueSize = $queue->count();

        $this->startTransaction('w');

        for ($i=0; $i<$queueSize; $i++) {
            /* @var $message Message*/
            $message = $queue->bottom();
            fputcsv(
                $this->storage,
                [
                    $message->getId(),
                    $message->getSecondsToExecute()
                ]
            );
            $queue->dequeue();
        }

        $this->finishTransaction();
    }

    /**
     * @param string $flag
     */
    private function startTransaction(string $flag)
    {
        $this->storage = fopen($this->fileName, $flag);
    }

    /**
     *
     */
    private function finishTransaction()
    {
        fclose($this->storage);
        unset($this->storage);
    }
}