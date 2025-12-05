<?php

namespace App\Filament\Clusters\Pendapatan;

use Filament\Clusters\Cluster;

class PendapatanCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
}
