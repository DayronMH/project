<?php

require_once '../models/salesModel.php';
require_once '../models/treesModel.php';

class SalesController
{
    private $salesModel;
    private $treesModel;

     /**
     * Constructs a new instance of the controller, initializing the `SalesModel` and `TreesModel` objects
     */
    public function __construct()
    {
        $this->salesModel = new SalesModel();
        $this->treesModel = new TreesModel();
    }

     /**
     * Creates a new sale and marks the corresponding tree as sold
     *
     * @param int $buyerId
     * @param int $treeId 
     * @return array
     */
    public function createSale(int $buyerId, int $treeId): array
    {
        if ($this->salesModel->createSale($buyerId, $treeId)) {
            $this->salesModel->markTreeAsSold($treeId);
            return ['status' => 'success', 'message' => 'Sale created successfully.'];

        } else {
            return ['status' => 'error', 'message' => 'Failed to create sale.'];
        }
    }

     /**
     * Retrieves a list of sales made by a specific buyer
     *
     * @param int $buyerId
     * @return array
     */
    public function getSalesByBuyer(int $buyerId): array
    {
        return $this->salesModel->getSalesByBuyerId($buyerId);
    }

     /**
     * Retrieves a list of sales for a specific tree
     *
     * @param int $treeId
     * @return array 
     */
    public function getSalesByTree(int $treeId): array
    {
        return $this->salesModel->getSalesByTreeId($treeId);
    }

     /**
     * Retrieves a list of trees purchased by a specific user
     *
     * @param int $userId
     * @return array 
     */
    public function getPurchasedTreesByUser(int $userId): array
    {
        return $this->treesModel->getPurchasedTreesByUser($userId);
    }
}