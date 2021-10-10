<?php

namespace APS;

class DBJoinParams
{

    /**
     * @var DBJoinParam
     */
    private $primary;

    /**
     * @var array [DBJoinParam]
     */
    private $list = [];

    private $restricted = false;

    public function __construct(DBJoinParam $primary)
    {
        $this->primary = $primary;
    }

    public static function init(DBJoinParam $primaryParam): DBJoinParams
    {
        return new static($primaryParam);
    }

    public function leftJoin(DBJoinParam $DBJoinParam): DBJoinParams
    {
        $this->list[] = $DBJoinParam;
        return $this;
    }


    /**
     * 限定查询排序方式
     * @param string $customFinalOrder
     * @param string $orientation
     * @return DBJoinParams
     */
    public function orderBy(string $customFinalOrder, string $orientation = DBOrder_ASC): DBJoinParams
    {

        $order = $customFinalOrder;
        $order_orientation = $orientation;

        return $this;
    }

    public function primaryConditionIgnored(): bool
    {
        return !$this->primary->hasFilter() && !$this->primary->hasCondition();
    }


    public function export(): string
    {
        $query = "";

        /** Primary Table */
        $query .= $this->exportFields();

        $query .= " FROM {$this->primary->getTable()} ";

        /** Join Tables */
        $query .= $this->exportJoin();

        $query .= $this->exportCondition();

        return $query;
    }

    public function exportFields(): string
    {
        $query = "";

        /** Primary Table */
        $query .= $this->primary->exportFields();

        /** Join Tables */
        for ($i = 0; $i < count($this->list); $i++) {
            if ($this->list[$i] instanceof DBJoinParam) {
                $query .= ", ";
                $query .= $this->list[$i]->exportFields();
            }
        }
        return $query;
    }

    public function exportPrimaryCount(): string{
        return "COUNT({$this->primary->getTable()}.*)";
    }

    public function exportCountJoinTables(): string{

        $query = "{$this->primary->getTable()} ";

        /** Join Tables */
        for ($i = 0; $i < count($this->list); $i++) {
            if ($this->list[$i] instanceof DBJoinParam && $this->list[$i]->hasCondition() ) {
                $query .= $this->list[$i]->exportJoin( true );
            }
        }
        return $query;
    }


    public function exportJoin(): string
    {
        $query = "";
        /** Join Tables */
        for ($i = 0; $i < count($this->list); $i++) {
            if ($this->list[$i] instanceof DBJoinParam) {
                $query .= $this->list[$i]->exportJoin();
            }
        }
        return $query;
    }

    public function exportCondition(): string
    {
        $query = "";

        if ($this->primary->hasCondition()) {
            $query .= $this->primary->exportCondition($this->restricted);
            $this->restricted = true;
        }

        /** Join Tables */
        for ($i = 0; $i < count($this->list); $i++) {
            if ($this->list[$i] instanceof DBJoinParam && $this->list[$i]->hasCondition()) {
                $query .= $this->list[$i]->exportCondition($this->restricted);
                $this->restricted = true;
            }
        }

        if ($this->primary->hasCondition()) {
            $query .= $this->primary->exportRestrict();
        }

        $this->restricted = false;
        return $query;
    }

    public function convertSubData( array &$data )
    {
        for ($i = 0; $i < count($this->list); $i++) {

            if( $this->list[$i] instanceof DBJoinParam && $this->list[$i]->isSub() ){

                $this->list[$i]->convertSubData( $data );
            }
        }
    }

    public function toArray(): array{

        $list = [
            'primary'=>[
                $this->primary->toArray()
            ],
            'joins'=>[]
        ];

        foreach ( $this->list as $i => $joinParam ){
            $list['joins'][] = $joinParam->toArray();
        }

        return $list;
    }
}