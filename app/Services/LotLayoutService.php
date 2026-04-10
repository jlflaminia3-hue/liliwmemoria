<?php

namespace App\Services;

use App\Models\Lot;
use InvalidArgumentException;

class LotLayoutService
{
    public function syncSection(string $section): int
    {
        $definition = config("lot_layouts.sections.{$section}");

        if (! is_array($definition)) {
            throw new InvalidArgumentException("Unknown lot layout section [{$section}].");
        }

        $records = match ($section) {
            'phase_1' => $this->buildPhase1Records($definition),
            'garden_lot' => $this->buildGardenLotRecords($definition),
            'narra' => $this->buildNarraRecords($definition),
            default => throw new InvalidArgumentException("Unsupported lot layout section [{$section}]."),
        };

        foreach ($records as $record) {
            $lot = Lot::query()->firstOrNew([
                'section' => $section,
                'lot_number' => $record['lot_number'],
            ]);

            $lot->geometry_type = 'rect';
            $lot->geometry = $record['geometry'];
            $lot->latitude = $record['latitude'];
            $lot->longitude = $record['longitude'];
            $lot->notes = $definition['note'];

            if (! $lot->exists) {
                $lot->name = 'Unassigned';
                $lot->status = 'available';
                $lot->is_occupied = false;
            }

            $lot->save();
        }

        return count($records);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildPhase1Records(array $definition): array
    {
        $layout = $definition['layout'];
        $imageHeight = (float) $definition['image_height'];
        $records = [];
        $lotNumber = 0;

        for ($row = 0; $row < (int) $layout['row_count']; $row++) {
            $activeColumns = $row < (int) $layout['upper_section_rows']
                ? (int) $layout['upper_section_columns']
                : (int) $layout['column_count'];

            for ($column = 0; $column < $activeColumns; $column++) {
                $x = round((float) $layout['left'] + ($column * (float) $layout['cell_width']), 2);
                $w = round((float) $layout['cell_width'], 2);
                $h = round((float) $layout['cell_height'], 2);
                $sourceY = round((float) $layout['top'] + ($row * (float) $layout['cell_height']), 2);
                $y = round($imageHeight - $sourceY - $h, 2);
                $lotNumber++;

                $records[] = $this->rectangleRecord($lotNumber, $x, $y, $w, $h);
            }
        }

        return $records;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildGardenLotRecords(array $definition): array
    {
        $layout = $definition['layout'];
        $imageHeight = (float) $definition['image_height'];
        $xBoundaries = array_map('floatval', $layout['x_boundaries']);
        $yBoundaryConfig = $layout['y_boundaries'];
        $yBoundaries = array_map('floatval', $yBoundaryConfig['top']);

        for (
            $y = (float) $yBoundaryConfig['middle_start'];
            $y <= (float) $yBoundaryConfig['middle_end'];
            $y += (float) $yBoundaryConfig['middle_step']
        ) {
            $yBoundaries[] = round($y, 2);
        }

        $yBoundaries[] = (float) $yBoundaryConfig['bottom'];

        $records = [];
        $lotNumber = 0;

        for ($row = 0; $row < count($yBoundaries) - 1; $row++) {
            $sourceY = (float) $yBoundaries[$row];
            $h = round((float) $yBoundaries[$row + 1] - $sourceY, 2);
            $y = round($imageHeight - $sourceY - $h, 2);

            for ($column = 0; $column < count($xBoundaries) - 1; $column++) {
                $x = round((float) $xBoundaries[$column], 2);
                $w = round((float) $xBoundaries[$column + 1] - (float) $xBoundaries[$column], 2);
                $lotNumber++;

                $records[] = $this->rectangleRecord($lotNumber, $x, $y, $w, $h);
            }
        }

        return $records;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildNarraRecords(array $definition): array
    {
        $layout = $definition['layout'];
        $imageHeight = (float) $definition['image_height'];
        $xBoundaries = array_map('floatval', $layout['x_boundaries']);
        $records = [];
        $lotNumber = 0;

        foreach (['top', 'bottom'] as $bandKey) {
            $band = $layout[$bandKey];
            $startColumn = (int) $band['start_column'];
            $yBoundaries = array_map('floatval', $band['y_boundaries']);

            for ($row = 0; $row < count($yBoundaries) - 1; $row++) {
                $sourceY = (float) $yBoundaries[$row];
                $h = round((float) $yBoundaries[$row + 1] - $sourceY, 2);
                $y = round($imageHeight - $sourceY - $h, 2);

                for ($column = $startColumn; $column < count($xBoundaries) - 1; $column++) {
                    $x = round((float) $xBoundaries[$column], 2);
                    $w = round((float) $xBoundaries[$column + 1] - (float) $xBoundaries[$column], 2);
                    $lotNumber++;

                    $records[] = $this->rectangleRecord($lotNumber, $x, $y, $w, $h);
                }
            }
        }

        return $records;
    }

    /**
     * @return array<string, mixed>
     */
    private function rectangleRecord(int $lotNumber, float $x, float $y, float $w, float $h): array
    {
        return [
            'lot_number' => $lotNumber,
            'latitude' => round($y + ($h / 2), 2),
            'longitude' => round($x + ($w / 2), 2),
            'geometry' => [
                'x' => $x,
                'y' => $y,
                'w' => $w,
                'h' => $h,
            ],
        ];
    }
}
