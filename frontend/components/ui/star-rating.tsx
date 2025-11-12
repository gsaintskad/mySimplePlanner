"use client";

import { useState } from "react";
import { Star } from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";

interface StarRatingProps {
  /** The current rating value (0-5) */
  value: number;
  /** Callback to set the rating. If not provided, component is read-only. */
  onChange?: (value: number) => void;
  /** If true, disables hover and click events. */
  readOnly?: boolean;
  /** Optional class names for the container. */
  className?: string;
  /** Optional class names for the Star icon. */
  starClassName?: string;
}

/**
 * A reusable star rating component.
 * It's interactive if `onChange` is provided and not `readOnly`.
 */
export function StarRating({
  value,
  onChange,
  readOnly = false,
  className,
  starClassName,
}: StarRatingProps) {
  const [hover, setHover] = useState(0);
  const isInteractive = onChange && !readOnly;

  const handleClick = (starValue: number) => {
    if (isInteractive) {
      onChange(starValue === value ? 0 : starValue); // Click same star to clear
    }
  };

  const handleHover = (starValue: number) => {
    if (isInteractive) {
      setHover(starValue);
    }
  };

  const handleLeave = () => {
    if (isInteractive) {
      setHover(0);
    }
  };

  const handleClear = () => {
    if (isInteractive) {
      onChange(0);
    }
  };

  return (
    <div className={cn("flex items-center gap-2", className)}>
      <div className="flex items-center gap-1" onMouseLeave={handleLeave}>
        {[1, 2, 3, 4, 5].map((starValue) => (
          <Star
            key={starValue}
            className={cn(
              "transition-colors",
              isInteractive && "cursor-pointer",
              hover >= starValue || (!hover && value >= starValue)
                ? "text-yellow-400 fill-yellow-400"
                : "text-gray-300 dark:text-gray-600",
              starClassName || "size-6" // Default size, can be overridden
            )}
            onClick={() => handleClick(starValue)}
            onMouseEnter={() => handleHover(starValue)}
          />
        ))}
      </div>
      {isInteractive && (
        <Button
          type="button"
          variant="ghost"
          size="sm"
          onClick={handleClear}
          className="text-xs text-muted-foreground h-auto px-2 py-1"
        >
          Clear
        </Button>
      )}
    </div>
  );
}
