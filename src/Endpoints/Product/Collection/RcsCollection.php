<?php

namespace RainYun\Endpoints\Product\Collection;

use RainYun\Collection;

/**
 * RcsCollection - Collection for RCS (Cloud Server) API responses.
 *
 * Provides convenient access to RCS instance data:
 * - $result->code - HTTP code (200)
 * - $result->data->TotalRecords - Total number of records
 * - $result->data->Records - Array of RCS instances
 *
 * Each RCS instance contains:
 * - ID, UID, PlanID, Status, HostName
 * - MainIPv4, CPU, Memory, Disk
 * - Node, Plan, OsInfo, UsageData
 * - And more detailed information
 */
class RcsCollection extends Collection
{
    /**
     * Check if the request was successful.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return isset($this->attributes['code']) && $this->attributes['code'] === 200;
    }

    /**
     * Get the response code.
     *
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->attributes['code'] ?? null;
    }

    /**
     * Get the data object containing TotalRecords and Records.
     *
     * @return Collection|null
     */
    public function getData()
    {
        return $this->attributes['data'] ?? null;
    }

    /**
     * Get all RCS instance records.
     *
     * @return array|null
     */
    public function getRecords(): ?array
    {
        $data = $this->getData();
        return $data ? $data->Records : null;
    }

    /**
     * Get total number of records.
     *
     * @return int
     */
    public function getTotalRecords(): int
    {
        $data = $this->getData();
        return $data && isset($data->TotalRecords) ? $data->TotalRecords : 0;
    }

    /**
     * Get RCS instances filtered by status.
     *
     * @param string $status Status to filter by (e.g., "running", "stopped")
     * @return array
     */
    public function getByStatus(string $status): array
    {
        $records = $this->getRecords();
        if (!$records) {
            return [];
        }

        return array_filter($records, function ($record) use ($status) {
            return isset($record->Status) && $record->Status === $status;
        });
    }

    /**
     * Get RCS instances filtered by region.
     *
     * @param string $region Region to filter by (e.g., "cn-nb1", "cn-hk4")
     * @return array
     */
    public function getByRegion(string $region): array
    {
        $records = $this->getRecords();
        if (!$records) {
            return [];
        }

        return array_filter($records, function ($record) use ($region) {
            return isset($record->Node->Region) && $record->Node->Region === $region;
        });
    }

    /**
     * Get RCS instance by ID.
     *
     * @param int $id Instance ID
     * @return Collection|null
     */
    public function getById(int $id)
    {
        $records = $this->getRecords();
        if (!$records) {
            return null;
        }

        foreach ($records as $record) {
            if (isset($record->ID) && $record->ID === $id) {
                return $record;
            }
        }

        return null;
    }

    /**
     * Get the count of RCS instances.
     *
     * @return int
     */
    public function count(): int
    {
        $records = $this->getRecords();
        return $records ? count($records) : 0;
    }
}
