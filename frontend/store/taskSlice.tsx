import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { Task } from "@/lib/types";

interface TaskState {
  tasks: Task[];
}

const initialState: TaskState = {
  tasks: [], // Mock data removed, starts empty
};

const taskSlice = createSlice({
  name: "tasks",
  initialState,
  reducers: {
    setTasks: (state, action: PayloadAction<Task[]>) => {
      state.tasks = action.payload;
    },
    addTask: (state, action: PayloadAction<Task>) => {
      state.tasks.push(action.payload);
    },
    // Updated payload type to number to match Task.id
    removeTask: (state, action: PayloadAction<number>) => {
      state.tasks = state.tasks.filter((task) => task.id !== action.payload);
    },
    updateTask: (state, action: PayloadAction<Task>) => {
      const index = state.tasks.findIndex(
        (task) => task.id === action.payload.id
      );
      if (index !== -1) {
        state.tasks[index] = action.payload;
      }
    },
  },
});

export const { setTasks, addTask, removeTask, updateTask } = taskSlice.actions;

export default taskSlice.reducer;
