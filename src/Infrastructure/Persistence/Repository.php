<?php
namespace Infrastructure\Persistence;

interface Repository
{

    /**
     * returns specific connection object for this kind of repository
     */
    public function getConnection();

    /**
     * persists given $object in repository
     */
    public function save($object);

    /**
     * fetches an object identified by given $id
     */
    public function get($id);

    /**
     * deletes object from repository identified by given $id
     */
    public function delete($id);

}
