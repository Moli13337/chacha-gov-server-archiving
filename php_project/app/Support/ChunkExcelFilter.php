<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/22
 * Time: 17:31
 */

namespace App\Support;


use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkExcelFilter implements IReadFilter
{
    protected $startRow = 0;
    protected $endRow = 0;

    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if (($row == 1) || ($row >= $this->startRow && $row < $this->endRow)) {
            return true;
        }

        return false;
    }
}