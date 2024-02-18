<?php

namespace InvoiceShelf\Bouncer\Scopes;

use Silber\Bouncer\Database\Scope\Scope;

class DefaultScope extends Scope
{
    public function applyToModelQuery($query, $table = null)
    {
        if (is_null($this->scope) || $this->onlyScopeRelations) {
            return $query;
        }

        if (is_null($table)) {
            $table = $query->getModel()->getTable();
        }

        return $this->applyToQuery($query, $table);
    }

    public function applyToRelationQuery($query, $table)
    {
        if (is_null($this->scope)) {
            return $query;
        }

        return $this->applyToQuery($query, $table);
    }

    protected function applyToQuery($query, $table)
    {
        return $query->where(function ($query) use ($table) {
            $query->where("{$table}.scope", $this->scope)
                ->orWhereNull("{$table}.scope");
        });
    }
}
