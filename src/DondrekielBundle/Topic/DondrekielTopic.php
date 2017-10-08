<?php

namespace DondrekielBundle\Topic;

use DondrekielBundle\Periodic\DondrekielPeriodic;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimerInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimer;
use Gos\Bundle\WebSocketBundle\Topic\ConnectionPeriodicTimer;

class DondrekielTopic implements TopicInterface, TopicPeriodicTimerInterface
{
    /**
     * @var TopicPeriodicTimer
     */
    protected $periodicTimer;

    protected $clientManipulator;

    /**
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function __construct(ClientManipulatorInterface $clientManipulator)
    {
        $this->clientManipulator = $clientManipulator;
    }

    /**
     * @param TopicPeriodicTimer $periodicTimer
     */
    public function setPeriodicTimer(TopicPeriodicTimer $periodicTimer)
    {
        $this->periodicTimer = $periodicTimer;
    }

    /**
     * @param Topic $topic
     *
     * @return array
     */
    public function registerPeriodicTimer(Topic $topic)
    {
        //add
        $this->periodicTimer->addPeriodicTimer($this, 'hello', 2, function () use ($topic) {
            $topic->broadcast('hello world');
        });

        //exist
        $this->periodicTimer->isPeriodicTimerActive($this, 'hello'); // true or false

        //remove
        $this->periodicTimer->cancelPeriodicTimer($this, 'hello');
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        //$topic->broadcast(['msg' => $connection->resourceId . " has joined " . $topic->getId()]);
        /** @var ConnectionPeriodicTimer $topicTimer */
        $topicTimer = $connection->PeriodicTimer;

        //Add periodic timer
        /*$topicTimer->addPeriodicTimer('hello', 2 * 60, function () use ($topic, $connection) {
            $connection->event($topic->getId(), ['msg' => 'hello world']);
        });*/

        $adminuser = $this->clientManipulator->findByUsername($topic, 'admin');
        if (false !== $adminuser) {
            $topic->broadcast('Hello admin', array(), array($adminuser['connection']->WAMP->sessionId));
        }

        //exist
        //$topicTimer->isPeriodicTimerActive('hello'); //true or false

        //Remove periodic timer
        //  $topicTimer->cancelPeriodicTimer('hello');
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => $connection->resourceId . " has left " . $topic->getId()]);
    }


    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @return mixed|void
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        /*
        	$topic->getId() will contain the FULL requested uri, so you can proceed based on that

            if ($topic->getId() === 'acme/channel/shout')
     	       //shout something to all subs.
        */

        $topic->broadcast([
            'msg' => $event,
        ]);
    }

    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName()
    {
        return 'dondrekiel.topic';
    }
}