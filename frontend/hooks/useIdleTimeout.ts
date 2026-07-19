'use client';

import { useEffect, useRef, useCallback } from 'react';

const IDLE_TIMEOUT = 2 * 60 * 60 * 1000; // 2 jam
const WARNING_BEFORE = 5 * 60 * 1000; // 5 menit sebelum logout

interface UseIdleTimeoutOptions {
  onLogout: () => void;
  onWarning?: () => void;
  onWarningDismissed?: () => void;
}

export default function useIdleTimeout({ onLogout, onWarning, onWarningDismissed }: UseIdleTimeoutOptions) {
  const idleTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  const warningTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  const logoutTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

  const clearAllTimers = useCallback(() => {
    if (idleTimer.current) clearTimeout(idleTimer.current);
    if (warningTimer.current) clearTimeout(warningTimer.current);
    if (logoutTimer.current) clearTimeout(logoutTimer.current);
    idleTimer.current = null;
    warningTimer.current = null;
    logoutTimer.current = null;
  }, []);

  const startTimers = useCallback(() => {
    clearAllTimers();

    // Timer untuk menampilkan peringatan (2 jam - 5 menit = 1 jam 55 menit)
    warningTimer.current = setTimeout(() => {
      onWarning?.();

      // Timer untuk auto logout setelah peringatan (5 menit)
      logoutTimer.current = setTimeout(() => {
        clearAllTimers();
        onLogout();
      }, WARNING_BEFORE);
    }, IDLE_TIMEOUT - WARNING_BEFORE);
  }, [clearAllTimers, onLogout, onWarning]);

  const handleActivity = useCallback(() => {
    // Reset semua timer saat ada aktivitas
    startTimers();
    onWarningDismissed?.();
  }, [startTimers, onWarningDismissed]);

  const extendSession = useCallback(() => {
    // Dipanggil saat user klik "Tetap di sini"
    startTimers();
    onWarningDismissed?.();
  }, [startTimers, onWarningDismissed]);

  useEffect(() => {
    const events = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'];
    
    events.forEach(event => {
      document.addEventListener(event, handleActivity, { passive: true });
    });

    // Mulai timer pertama kali
    startTimers();

    return () => {
      events.forEach(event => {
        document.removeEventListener(event, handleActivity);
      });
      clearAllTimers();
    };
  }, [handleActivity, startTimers, clearAllTimers]);

  return { extendSession };
}
