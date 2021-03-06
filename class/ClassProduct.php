<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClassProduct
 *
 * @author Massimiliano Palermo <maxx.palermo@gmail.com>
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "ClassDB.php";

class ClassProduct {
    private $fk_customer;
    private $fk_warehouse;
    private $fk_pricelist;
    private $db;
    private $tablename;
    private $classDB;
    
    public function __construct(DoliDBMysqli $db) 
    {
        $this->classDB = new ClassDB($db);
        $this->db = $db;
        $this->tablename = MAIN_DB_PREFIX . "product";
        
        $tablename = [
            MAIN_DB_PREFIX . "pos"
        ];
        
        $fields = [
            "fk_customer",
            "fk_warehouse",
            "fk_pricelist",
        ];
        
        $where = [
            "rowid=1",
        ];
        
        $query = $this->classDB->Select($tablename, $fields, $where);
        $rs = $db->query($query);
        if($rs)
        {
            $result = $db->fetch_object($rs);
            $this->fk_customer  = $result->fk_customer;
            $this->fk_warehouse = $result->fk_warehouse;
            $this->fk_pricelist = $result->fk_pricelist;
        }
    }
    
    /**
     * 
     * @param mysqli_result $result : Resultset of last query
     * @return Object : Product Object 
     * @author Massimiliano Palermo <maxx.palermo@gmail.com>
     * @version 1.0
     * @copyright (c) 2016, Massimiliano Palermo <http://www.mpsoft.it> 
     */
    public function getProduct(mysqli_result $result)
    {
        try 
        {
            while ($record = $this->db->fetch_object($result))       
            {
                $product                = new stdClass();
                $product->rowid         = $record->rowid;
                $product->ref           = $record->ref;
                $product->label         = $record->label;
                $product->isBatch       = $record->tobatch;
                $product->barcode       = $record->barcode;
                $product->qty           = $record->stock;
                $product->fk_stock      = $record->stock;
                $product->price         = number_format($record->price,2);
                $product->price_ttc     = number_format($record->price_ttc,2);
                $product->tva_tx        = number_format($record->tva_tx,2);

                $products[] = $product;
            }

            return $products;
        } 
        catch (Exception $ex) 
        {
            print $ex->getCode() . ": " . $ex->getMessage();
        }
    }
    
    private function getFields()
    {
        $fields = [
            "rowid",
            "ref",
            "label",
            "price",
            "price_ttc",
            "tva_tx",
            "tobatch",
            "barcode",
            "stock",
        ];
        
        return $fields;
    }
    
    /**
     * 
     * @param String $barcode : barcode of the product to find
     * @return Json : Json object of selected product
     * @author Massimiliano Palermo <maxx.palermo@gmail.com>
     * @version 1.0
     * @copyright (c) 2016, Massimiliano Palermo <http://www.mpsoft.it> 
     */
    public function getProductByBarCode($barcode)
    {
        
        $fields = $this->getFields();
        $where = [
            "barcode = '$barcode'",
        ];
        $tablename = [
            $this->tablename,
        ];
        $query = $this->classDB->Select($tablename, $fields, $where);
        $ret = $this->db->query($query);
        if($ret->num_rows)
        {
            //Return result to jTable
            return json_encode($this->getProduct($ret));
        }
        else
        {
            return "";
        }
    }
    
    /**
     * 
     * @param String $ref : ref code of the product to find
     * @return Json : Json object of selected product
     * @author Massimiliano Palermo <maxx.palermo@gmail.com>
     * @version 1.0
     * @copyright (c) 2016, Massimiliano Palermo <http://www.mpsoft.it> 
     */
    public function getProductByRef($ref)
    {
        $fields = $this->getFields();
        $where = [
            "ref like '$ref%' LIMIT 50",
        ];
        $tablename = [
            $this->tablename,
        ];
        $query = $this->classDB->Select($tablename, $fields, $where);
        $ret = $this->db->query($query);
        if($ret->num_rows)
        {
            //Return result to jTable
            return json_encode($this->getProduct($ret));
        }
        else
        {
            return "";
        }
    }
    
    /**
     * 
     * @param String $label : label of the product to find
     * @return Json : Json object of selected product
     * @author Massimiliano Palermo <maxx.palermo@gmail.com>
     * @version 1.0
     * @copyright (c) 2016, Massimiliano Palermo <http://www.mpsoft.it> 
     */
    public function getProductByLabel($label)
    {
        $fields = $this->getFields();
        $where = [
            "label like '$label%' LIMIT 20",
        ];
        $tablename = [
            $this->tablename,
        ];
        $query = $this->classDB->Select($tablename, $fields, $where);
        $ret = $this->db->query($query);
        if($ret->num_rows)
        {
            //Return result to jTable
            return json_encode($this->getProduct($ret));
        }
        else
        {
            return "";
        }
    }
}
