import React, { useState, useEffect } from 'react';
import styles from './AuthenticatedUserDisplay.module.css';

const AuthenticatedUserDisplay = ({ token, apiUrl = '/local/courseapi/api' }) => {
  const [isLoading, setIsLoading] = useState(true);
  const [userData, setUserData] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchUserData = async () => {
      // Check for token in props or global location
      const authToken = token || window.MoodleReact?.token;
      
      if (!authToken) {
        setError('Authentication token not found.');
        setIsLoading(false);
        return;
      }

      try {
        const response = await fetch(`${apiUrl}/user/me`, {
          headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          throw new Error('Failed to fetch user data');
        }

        const data = await response.json();
        setUserData(data);
      } catch (err) {
        setError('Failed to fetch user data.');
      } finally {
        setIsLoading(false);
      }
    };

    fetchUserData();
  }, [token, apiUrl]);

  if (isLoading) {
    return <div className={styles.loading}>Loading...</div>;
  }

  if (error) {
    return <div className={styles.error}>Error: {error}</div>;
  }

  if (userData) {
    return (
      <div className={styles.welcome}>
        Welcome, {userData.firstname} {userData.lastname}!
      </div>
    );
  }

  return null;
};

export default AuthenticatedUserDisplay;