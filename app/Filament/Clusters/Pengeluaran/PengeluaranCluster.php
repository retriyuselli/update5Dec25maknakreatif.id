<?php

namespace App\Filament\Clusters\Pengeluaran;

use Filament\Clusters\Cluster;

class PengeluaranCluster extends Cluster
{
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';
}
