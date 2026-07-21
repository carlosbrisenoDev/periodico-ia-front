import { useEffect, useRef } from 'react';
import { useLocation } from 'react-router-dom';

const API_URL = import.meta.env.VITE_API_URL;

export const trackView = async (url: string) => {
  try {
    await fetch(`${API_URL}/api/v1/analytics/views`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ url })
    });
  } catch (err) {
    console.error('Local express analytics tracking failed', err);
  }
};

export const trackTab = async (url: string, tabName: string) => {
  try {
    await fetch(`${API_URL}/api/v1/analytics/tabs`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ url, tabName })
    });
  } catch (err) {
    console.error('Local express analytics tracking failed', err);
  }
};

export const trackNavigation = async (from: string, to: string) => {
  try {
    await fetch(`${API_URL}/api/v1/analytics/navigation`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ from, to })
    });
  } catch (err) {
    console.error('Local express analytics tracking failed', err);
  }
};

export const trackTime = async (url: string, timeSpent: number) => {
  try {
    await fetch(`${API_URL}/api/v1/analytics/time`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ url, timeSpent })
    });
  } catch (err) {
    console.error('Local express analytics tracking failed', err);
  }
};

export const useAnalytics = () => {
  const location = useLocation();
  const prevPath = useRef<string | null>(null);
  const startTime = useRef<number>(Date.now());

  useEffect(() => {
    const currentPath = location.pathname;
    
    // Log pure view
    trackView(currentPath);

    // Log navigation if we have a previous path
    if (prevPath.current && prevPath.current !== currentPath) {
      trackNavigation(prevPath.current, currentPath);
    }

    // Reset start time for the new path
    startTime.current = Date.now();
    prevPath.current = currentPath;

    return () => {
        // Calculate time spent when unmounting or changing path
        const timeSpent = Math.floor((Date.now() - startTime.current) / 1000);
        if (timeSpent > 0) {
            trackTime(currentPath, timeSpent);
        }
    };
  }, [location.pathname]);
};
