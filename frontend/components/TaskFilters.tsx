"use client";

import { Task } from "@/lib/types";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Label } from "@/components/ui/label";

export type SortOrder = "newest" | "oldest";
export type RatingFilter = "all" | "0" | "1" | "2" | "3" | "4" | "5";

export interface TaskFiltersState {
  rating: RatingFilter;
  sort: SortOrder;
}

interface TaskFiltersProps {
  filters: TaskFiltersState;
  onFiltersChange: (newFilters: Partial<TaskFiltersState>) => void;
}

export function TaskFilters({ filters, onFiltersChange }: TaskFiltersProps) {
  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-card border rounded-lg">
      {/* Filter by Rating */}
      <div className="space-y-2">
        <Label htmlFor="filter-rating">Filter by Priority</Label>
        <Select
          value={filters.rating}
          onValueChange={(value) =>
            onFiltersChange({ rating: value as RatingFilter })
          }
        >
          <SelectTrigger id="filter-rating">
            <SelectValue placeholder="Select priority" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All Priorities</SelectItem>
            {[5, 4, 3, 2, 1, 0].map((num) => (
              <SelectItem key={num} value={String(num)}>
                {num} {num === 1 ? "Star" : "Stars"}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      {/* Sort by Date */}
      <div className="space-y-2">
        <Label htmlFor="sort-date">Sort by Date Created</Label>
        <Select
          value={filters.sort}
          onValueChange={(value) =>
            onFiltersChange({ sort: value as SortOrder })
          }
        >
          <SelectTrigger id="sort-date">
            <SelectValue placeholder="Sort by" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="newest">Newest First</SelectItem>
            <SelectItem value="oldest">Oldest First</SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>
  );
}
