'use client';

import { useState, useRef, useEffect } from 'react';
import { MoreVertical } from 'lucide-react';

interface ActionItem {
  label: string;
  icon: any;
  onClick: () => void;
  variant?: 'default' | 'danger' | 'success';
  disabled?: boolean;
}

export default function DropdownMenu({ actions }: { actions: ActionItem[] }) {
  const [isOpen, setIsOpen] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <div className="relative flex justify-end" ref={menuRef}>
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="p-1.5 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer"
      >
        <MoreVertical className="h-4 w-4" />
      </button>
      {isOpen && (
        <div className="absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-1">
          {actions.map((action, index) => (
            <button
              key={index}
              onClick={() => {
                action.onClick();
                setIsOpen(false);
              }}
              disabled={action.disabled}
              className={`w-full flex items-center gap-2 px-3 py-2 text-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed ${
                action.variant === 'danger'
                  ? 'text-red-600 hover:bg-red-50'
                  : action.variant === 'success'
                  ? 'text-green-600 hover:bg-green-50'
                  : 'text-gray-700 hover:bg-gray-50'
              }`}
            >
              <action.icon className="h-4 w-4 shrink-0" />
              {action.label}
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
