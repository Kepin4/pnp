<?php

namespace App\Models;

use CodeIgniter\Model;

class Base_model extends Model
{
    public function use($query, $str = false)
    {
        if ($str) {
            return $query;
        }
        return $this->db->query($query)->getResult();
    }

    public function exec($query, $str = false)
    {
        if ($str) {
            return $query;
        }
        return $this->db->query($query);
    }


    public function trunc($query)
    {
        $queries = explode(';', $query);
        foreach ($queries as $qry) {
            $qry = trim($qry);
            if (!empty($qry)) {
                $this->db->query($qry);
            }
        }
    }

    public function usefirst($query, $str = false)
    {
        if ($str) {
            return $query;
        }
        return $this->db->query($query)->getRow();
    }

    public function uselast($query, $str = false)
    {
        if ($str) {
            return $query;
        }
        return $this->db->query($query)->getLastRow();
    }

    public function sel($table, $where = [])
    {
        if (!empty($where)) {
            return $this->db->table($table)->getWhere($where)->getResultObject();
        }
        return $this->db->table($table)->get()->getResultObject();
    }

    public function ins($table, $data, $str = false)
    {
        $xDB = $this->db->table($table);
        $xDB->set($data);
        if ($str) {
            return $xDB->getCompiledInsert(false);
        }
        return $xDB->insert();
    }
    public function insbulk($table, $data)
    {
        return $this->db->table($table)->insertBatch($data);
    }
    public function insID($table, $data): int
    {
        $this->db->table($table)->insert($data);
        return $this->db->insertID();
    }
    public function upd($table, $data, $where = null)
    {
        return $this->db->table($table)->update($data, $where);
    }
    public function del($table, $where = null)
    {
        if ($where === null) {
            return $this->db->table($table)->emptyTable();
        } else {
            return $this->db->table($table)->delete($where);
        }
    }
    public function getWaktu($format = 'Y-m-d H:i:s')
    {
        $query = $this->db->query("SELECT DATE_SUB(sysdate(), INTERVAL 1 HOUR) as dt;")->getRow();

        if ($query && isset($query->dt)) {
            $dateTime = new \DateTime($query->dt);
            return $dateTime->format($format);
        }

        return date($format);
    }

    public function helper($x)
    {
        if (is_null($x) || $x === '') {
            return '';
        }
        return $this->db->escape($x);
    }

    function logPHP($data)
    {
        $output = $data;
        if (is_array($output)) $output = implode(',', $output);
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
}
