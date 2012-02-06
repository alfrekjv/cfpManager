<?php
namespace App\Data;
use App\Entity\Talk as TalkEntity;

class Talk extends \PPI\DataSource\ActiveQuery {
	
	protected $_meta = array(
		'conn'      => 'main',
		'table'     => 'talk',
		'primary'   => 'id',
		'fetchmode' => \PDO::FETCH_ASSOC
	);
	
	function create($data) {
		
		return $this->insert($data);
		
	}
	
	/**
	 * Get all the talks
	 * 
	 * @return mixed
	 */
	function getAll() {
		$rows = $this->fetchAll();
		$talks = array();
		foreach($rows as $row) {
			$talks[] = new TalkEntity($row);
		}
		return $talks;
	}
	
	function getByOwnerID($ownerID) {
		$rows =  $this->_conn->createQueryBuilder()
			->select('t.*')
			->from($this->_meta['table'], 't')
			->andWhere('t.owner_id = :ownerID')
			->setParameter(':ownerID', $ownerID)
			->orderBy('t.title', 'ASC')
			->execute()
			->fetchAll($this->_meta['fetchmode']);
		
		$result = array();
		foreach($rows as $row) {
			$result[] = new TalkEntity($row);
		}
		return $result;
	}
	
	function getTalkFromID($talkID) {
		return new TalkEntity($this->find($talkID));
	}
	
	function remove($talkID) {
		
		// Remove all votes, comments and CFP submissions on this talk
		$voteDataStorage       = new \App\Data\Vote();
		$commentDataStorage    = new \App\Data\Comment();
		$cfpSubmissionsStorage = new \App\Data\Conference\Submission();

		$cfpSubmissionsStorage->deleteByTalkID($talkID);
		
	}
	
}