<?php

/**
 * @author RonyLee <RonyLee.Lyz@gmail.com>
 * @date   2017/7/2
 */
namespace Sms\Model;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;


/**
 * Class BaseModel
 * @package Medal\Model
 */
class BaseModel
{

    public $db;

    /**
     * BaseModel constructor.
     */
    public function __construct()
    {
        $config = new Configuration();
        $connectionParam = require_once dirname(dirname(dirname(__DIR__))) . '/../config/Sms/database.php';
        $this->db = DriverManager::getConnection($connectionParam, $config);
    }

    /**
     * 保存记录
     * @param $columns
     */
    public function insert($tableName, $columns)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->insert($tableName);

        $n = 0;
        foreach ($columns as $key => $value) {
            $queryBuilder->setValue($key, '?')->setParameter($n, $value);
            $n++;
        }

        $result = $queryBuilder->execute();

        return $result ? $this->db->lastInsertId() : $result;
    }

    /**
     * 更新记录
     * @param $columns
     * @param $where
     */
    public function update($tableName, $id, $columns)
    {
        if (empty($columns)) return 0;

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->update($tableName);

        $n = 0;
        foreach ($columns as $key => $value) {
            $queryBuilder->set($key, '?')->setParameter($n, $value);
            $n++;
        }

        $where = $queryBuilder->expr()->eq('id', $id);
        $queryBuilder->where($where);

        return $queryBuilder->execute();
    }

    /**
     * 删除记录
     * @param $id
     * @return \Doctrine\DBAL\Driver\Statement|int
     */
    public function delete($tableName, $id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder
            ->delete($tableName)
            ->where($queryBuilder->expr()->eq('id', $id));

        return $queryBuilder->execute();
    }

    /**
     * 返回一条记录
     * @param array $where
     * @return mixed
     */
    public function getOne($tableName, $where = [])
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from($tableName);

        $n = 0;
        foreach ($where as $key => $value) {
            if ($n == 0) {
                $queryBuilder->where($key . ' ?');
            } else {
                $queryBuilder->andWhere($key . ' ?');
            }
            $queryBuilder->setParameter($n, $value);
            $n++;
        }

        return $queryBuilder->execute()->fetch();
    }

    /**
     * 返回多条记录
     * @param array  $where   ['id >' => 1,'id <' => 1]
     * @param array  $orderBy ['id' => 'desc','create_time' => 'asc']
     * @param string $offset  0
     * @param string $limit   10
     * @return array
     */
    public function getList($tableName, $where = [], $orderBy = [], $offset = '', $limit = '')
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('*')->from($tableName);

        $n = 0;
        foreach ($where as $key => $value) {
            if ($n == 0) {
                $queryBuilder->where($key . ' ?');
            } else {
                $queryBuilder->andWhere($key . ' ?');
            }
            $queryBuilder->setParameter($n, $value);
            $n++;
        }

        $n = 0;
        foreach ($orderBy as $key => $value) {
            if ($n == 0) {
                $queryBuilder->orderBy($key, $value);
            } else {
                $queryBuilder->addOrderBy($key, $value);
            }
            $n++;
        }

        if (!empty($offset)) {
            $queryBuilder->setFirstResult($offset);
        }

        if (!empty($limit)) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder->execute()->fetchAll();
    }


}