<?php

namespace Tests\Unit;

use App\Models\Deployment;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class InventoryModelTest extends TestCase
{
    public function test_inventory_deployment_items_relation_uses_the_deployment_model(): void
    {
        $inventory = new Inventory();

        $relation = $inventory->deploymentItems();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(Deployment::class, $relation->getRelated());
    }
}
