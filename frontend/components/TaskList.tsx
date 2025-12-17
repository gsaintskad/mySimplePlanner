"use client";

import { useMemo, useState } from "react";
import { useGetTasksQuery } from "@/store/authApi";
import { Task } from "@/lib/types";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { StarRating } from "@/components/ui/star-rating";
import { TaskFilters, TaskFiltersState } from "@/components/TaskFilters";
import { Loader2, Calendar, Pencil } from "lucide-react";
import { Button } from "@/components/ui/button";
import { NewTaskModal } from "./NewTaskForm";

// --- TaskCard Sub-Component ---
function TaskCard({ task }: { task: Task }) {
  const [isEditOpen, setIsEditOpen] = useState(false);

  return (
    <Dialog>
      <DialogTrigger asChild>
        <Card className="cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900 transition-colors">
          <CardHeader>
            <div className="flex justify-between items-start">
              <CardTitle className="text-lg">{task.title}</CardTitle>
              <StarRating
                value={task.priority}
                readOnly
                starClassName="size-4"
              />
            </div>
            <CardDescription className="flex items-center gap-1">
              <Calendar className="h-3 w-3" />
              Due: {new Date(task.due_date).toLocaleDateString()}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <p className="text-sm text-muted-foreground line-clamp-2">
              {task.description || "No description."}
            </p>
          </CardContent>
        </Card>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <div className="flex justify-between items-start pr-8">
            <div>
              <DialogTitle>{task.title}</DialogTitle>
              <DialogDescription>
                Created: {new Date(task.created_at).toLocaleString()}
              </DialogDescription>
            </div>
            {/* New Task Modal used for editing */}
            <NewTaskModal
              taskToEdit={task}
              open={isEditOpen}
              setOpen={setIsEditOpen}
              trigger={
                <Button variant="outline" size="sm">
                  <Pencil className="h-4 w-4 mr-2" />
                  Edit
                </Button>
              }
            />
          </div>
        </DialogHeader>
        <div className="py-4 space-y-4">
          <div>
            <h4 className="font-medium mb-1">Description</h4>
            <p className="text-sm text-muted-foreground whitespace-pre-wrap">
              {task.description || "No description provided."}
            </p>
          </div>
          <div className="flex justify-between items-center">
            <div>
              <h4 className="font-medium mb-1">Priority</h4>
              <StarRating value={task.priority} readOnly />
            </div>
            <div className="text-right">
              <h4 className="font-medium mb-1">Due Date</h4>
              <p className="text-sm">
                {new Date(task.due_date).toLocaleString()}
              </p>
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}

// --- Main TaskList Component ---
export function TaskList() {
  const { data: allTasks = [], isLoading, error } = useGetTasksQuery();
  const [filters, setFilters] = useState<TaskFiltersState>({
    rating: "all",
    sort: "newest",
  });

  const handleFiltersChange = (newFilters: Partial<TaskFiltersState>) => {
    setFilters((prev) => ({ ...prev, ...newFilters }));
  };

  const filteredTasks = useMemo(() => {
    let tasks = [...allTasks];
    if (filters.rating !== "all") {
      const ratingNum = parseInt(filters.rating);
      tasks = tasks.filter((task) => task.priority === ratingNum);
    }
    tasks.sort((a, b) => {
      const dateA = new Date(a.created_at).getTime();
      const dateB = new Date(b.created_at).getTime();
      return filters.sort === "newest" ? dateB - dateA : dateA - dateB;
    });
    return tasks;
  }, [allTasks, filters]);

  if (isLoading) {
    return (
      <div className="flex justify-center py-10">
        <Loader2 className="h-8 w-8 animate-spin text-blue-500" />
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-10 text-red-500">
        Failed to load tasks. Please try again later.
      </div>
    );
  }

  return (
    <div className="mt-6 space-y-4">
      <h2 className="text-2xl font-semibold">Your Tasks</h2>
      <TaskFilters filters={filters} onFiltersChange={handleFiltersChange} />
      {filteredTasks.length === 0 ? (
        <Card>
          <CardContent className="pt-6">
            <p className="text-center text-muted-foreground">
              No tasks found. Create one above!
            </p>
          </CardContent>
        </Card>
      ) : (
        <div className="space-y-4">
          {filteredTasks.map((task) => (
            <TaskCard key={task.id} task={task} />
          ))}
        </div>
      )}
    </div>
  );
}
