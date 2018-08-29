<?php

namespace DondrekielAppBundle\Topic;

use DondrekielAppBundle\Repository\ActionRepository;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimerInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimer;
use Gos\Bundle\WebSocketBundle\Topic\ConnectionPeriodicTimer;
use DondrekielAppBundle\Entity\Position;
use DondrekielAppBundle\Entity\Action;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\Message;
use DondrekielAppBundle\Repository\MessageRepository;
use DondrekielAppBundle\Repository\TeamRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Twig_Environment as TwigEnvironment;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;


class DondrekielTopic implements TopicInterface, TopicPeriodicTimerInterface
{
    /**
     * @var TopicPeriodicTimer
     */
    protected $periodicTimer;

    protected $clientManipulator;

    private $securityTokenStorage;

    private $entityManager;

    /**
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var ActionRepository
     */
    private $actionRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function __construct(ClientManipulatorInterface $clientManipulator, TokenStorage $securityTokenStorage, $doctrine, LoggerInterface $logger = null)
    {
        $this->clientManipulator = $clientManipulator;
        $this->securityTokenStorage = $securityTokenStorage;
        $this->entityManager = $doctrine->getEntityManager();
        $this->teamRepository = $doctrine->getRepository("DondrekielAppBundle:Team");
        $this->messageRepository = $doctrine->getRepository("DondrekielAppBundle:Message");
        $this->actionRepository = $doctrine->getRepository("DondrekielAppBundle:Action");
        $this->logger = null === $logger ? new NullLogger() : $logger;
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
        $this->periodicTimer->addPeriodicTimer($this, 'message', 10, function () use ($topic) {
            $messages = $this->messageRepository->findAllUnread();
            foreach ($messages as $message) {
                /*
                 * @var Message $message
                 */
                $topic->broadcast([
                    'message' =>
                        [
                            'title' => 'Nachrichen von der Spielleitung!',
                            'text' => $message->getMessageText()
                        ]
                ]);
                $message->setSendTime(time());
                $this->entityManager->persist($message);
                $this->entityManager->flush();
                $this->logger->info('Broadcast message to teams.');
            }
        });
        $this->periodicTimer->addPeriodicTimer($this, 'station_update', 10, function () use ($topic) {
            $actionList = $this->actionRepository->findAllUnread();
            foreach ($actionList as $action) {
                /*
                 * @var $action Action
                 */
                $topic->broadcast([
                    'station_update' =>
                        [
                            'status' => $action->GetAction(),
                            'station' => $action->getStation()->getId()
                        ]
                ]);
                $action->setSendTime(time());
                $this->entityManager->persist($action);
                $this->entityManager->flush();
                $this->logger->info('Broadcast station update to teams.');
            }
        });
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
        //$topicTimer = $connection->PeriodicTimer;

        //Add periodic timer
        //$topicTimer->addPeriodicTimer('hello', 5, function () use ($topic, $connection) {
//            $connection->event($topic->getId(), 'hello team ' . $topic->getId());
        //      });
        //    $adminuser = $this->clientManipulator->findByUsername($topic, 'admin');
        //  if (false !== $adminuser) {
        //    $topic->broadcast('Hello admin', array(), array($adminuser['connection']->WAMP->sessionId));
        //}

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
        $topic->broadcast(['message' => $connection->resourceId . " has left " . $topic->getId()]);
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
        $user = $this->clientManipulator->getClient($connection);
        /* @var Team $currentTeam */
//        $currentTeam = $this->securityTokenStorage->getToken()->getUser();

        if (is_array($event) && array_key_exists('position', $event)) {
            $currentTeam = $this->teamRepository->find($event['position']['team']);
        }


        if ($currentTeam) {
            if (is_string($currentTeam)) {
                /* @var Team $currentTeam */
                $currentTeam = $this->teamRepository->findByUsername($currentTeam);
            } elseif (is_object($currentTeam)) {
                /* @var Team $currentTeam */
                $currentTeam = $this->teamRepository->find($currentTeam->getId());
            }
        } else {
            $this->logger->error($currentTeam . ' could not be found');
        }

        if (!$currentTeam) {
            $this->logger->error($currentTeam . ' could not be found');
            return;
        }

        if (!$currentTeam->getIsTeam()) {
            $this->logger->warn($currentTeam . ' is not a team');
            return;
        }

        if (is_array($event) && array_key_exists('position', $event)) {
            $this->logger->info($currentTeam->getUsername() . ' set position');
            $position = new Position();
            $position->setLocationLat($event['position']['latitude']);
            $position->setLocationLng($event['position']['longitude']);
            $position->setTeam($currentTeam);
            $position->setTimestamp(time());

            $this->entityManager->persist($position);

            $currentTeam->setLocationLat($event['position']['latitude']);
            $currentTeam->setLocationLng($event['position']['longitude']);

            $this->entityManager->persist($currentTeam);
            $this->entityManager->flush();

        }

        //$event['team'] = $currentTeam->getUsername();
        //if ($event['position']['team'] == 3) {
        //    $event['position']['latitude'] = $event['position']['latitude'] + floatval(rand(1, 10) / 1000);
        //    $event['position']['longitude'] = $event['position']['longitude'] + floatval(rand(1, 10) / 1000);
        //}

        $topic->broadcast($event);
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
