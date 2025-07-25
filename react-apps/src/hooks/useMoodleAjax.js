import { useState, useCallback } from 'react';

/**
 * Custom hook for making AJAX calls to Moodle web services
 * Uses the standard Moodle AJAX service endpoint
 */
export const useMoodleAjax = () => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const callService = useCallback(async (methodname, args = {}) => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch('/lib/ajax/service.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify([{
          index: 0,
          methodname,
          args,
        }])
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const results = await response.json();
      
      // Moodle returns an array of results
      if (results && results.length > 0) {
        const result = results[0];
        
        if (result.error) {
          throw new Error(result.exception?.message || result.error);
        }
        
        setData(result.data);
        return result.data;
      }

      throw new Error('No data returned from service');
    } catch (err) {
      console.error('Moodle AJAX error:', err);
      setError(err);
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  return { data, loading, error, callService };
};