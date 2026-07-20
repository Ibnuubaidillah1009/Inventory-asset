'use client';

import { useState, useEffect, useCallback } from 'react';

interface CurrencyInputProps {
  value: string;
  onChange: (rawValue: string) => void;
  placeholder?: string;
  className?: string;
  min?: number;
  max?: number;
  disabled?: boolean;
  required?: boolean;
}

export default function CurrencyInput({
  value,
  onChange,
  placeholder = '0',
  className = '',
  min,
  max,
  disabled = false,
  required = false,
}: CurrencyInputProps) {
  const [displayValue, setDisplayValue] = useState('');

  useEffect(() => {
    if (value === '' || value === null || value === undefined) {
      setDisplayValue('');
    } else {
      const num = Number(value);
      if (!isNaN(num) && num !== 0) {
        setDisplayValue(num.toLocaleString('id-ID'));
      } else if (value === '0') {
        setDisplayValue('0');
      } else {
        setDisplayValue(value);
      }
    }
  }, [value]);

  const handleChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
    const raw = e.target.value.replace(/[^0-9]/g, '');
    if (raw === '') {
      setDisplayValue('');
      onChange('');
      return;
    }
    const num = Number(raw);
    if (min !== undefined && num < min) return;
    if (max !== undefined && num > max) return;
    setDisplayValue(num.toLocaleString('id-ID'));
    onChange(raw);
  }, [onChange, min, max]);

  const handleFocus = useCallback(() => {
    setDisplayValue((prev) => prev);
  }, []);

  return (
    <input
      type="text"
      inputMode="numeric"
      value={displayValue}
      onChange={handleChange}
      onFocus={handleFocus}
      placeholder={placeholder}
      className={className}
      disabled={disabled}
      required={required}
      autoComplete="off"
    />
  );
}
