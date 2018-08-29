<?php

namespace DondrekielAppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use DondrekielAppBundle\Entity\Team;

class TeamRepository extends EntityRepository implements UserProviderInterface
{

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        return $this->findOneByUsername($username);
    }

    public function getAllActiveTeams()
    {
        $result = $this->_em->createQuery("SELECT  t.id AS team_id, t.username AS username, t.locationLng AS locationLng, t.locationLat AS locationLat 
FROM DondrekielAppBundle\Entity\Team t WHERE t.status=2 AND t.isTeam=1")
            /*        $result = $this->_em->createQuery("SELECT  t.id AS team_id, t.username AS username, p1.locationLng AS location_lng, p1.locationLat AS location_lat
            FROM DondrekielAppBundle\Entity\Team t
            JOIN DondrekielAppBundle\Entity\Position p1 WITH (t.id = p1.team)
            LEFT OUTER JOIN DondrekielAppBundle\Entity\Position p2
              WITH (t.id = p2.team AND (p1.timestamp < p2.timestamp OR p1.timestamp = p2.timestamp AND p1.id < p2.id)) WHERE p2.id IS NULL")*/
            /*SELECT t.id AS team_id, t.username AS username, p1.locationLng AS location_lng, p1.locationLat AS location_lat FROM DondrekielAppBundle\Entity\Team t, DondrekielAppBundle\Entity\Position p1 WHERE t.id=p1.team AND p1.timestamp=(SELECT MAX(p2.timestamp) FROM DondrekielAppBundle\Entity\Position p2 WHERE p2.team=p1.team)")*/
            ->execute();
        if (count($result)) {
            return $result;
        }
        return false;
    }

    /*
 * SELECT p1.*, t.* FROM team t, position p1 WHERE t.id=p1.team_id AND p1.timestamp= (SELECT MAX(p2.timestamp) FROM position p2 WHERE p2.team_id=p1.team_id);
 * */


    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return true;
    }

}
