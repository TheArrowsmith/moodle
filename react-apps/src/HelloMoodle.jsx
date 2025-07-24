import React, { useState, useEffect } from 'react'
import styles from './HelloMoodle.module.css'

function HelloMoodle({ 
  userName = 'Guest', 
  courseName = 'Unknown Course',
  showTime = true,
  theme = 'light'
}) {
  const [currentTime, setCurrentTime] = useState(new Date())
  const [clickCount, setClickCount] = useState(0)
  
  useEffect(() => {
    if (showTime) {
      const timer = setInterval(() => {
        setCurrentTime(new Date())
      }, 1000)
      
      return () => clearInterval(timer)
    }
  }, [showTime])
  
  const handleClick = () => {
    setClickCount(prev => prev + 1)
    
    // Trigger Moodle custom event
    if (window.require) {
      try {
        window.require(['core/pubsub'], function(PubSub) {
          PubSub.publish('moodle-react/hello-clicked', {
            userName,
            courseName,
            clickCount: clickCount + 1,
            timestamp: new Date().toISOString()
          })
        })
      } catch (e) {
        console.log('Moodle AMD not available:', e)
      }
    }
  }
  
  const formatTime = (date) => {
    return date.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    })
  }
  
  return (
    <div className={`${styles.container} ${styles[theme]}`}>
      <h2 className={styles.title}>Hello from React in Moodle!</h2>
      
      <div className={styles.info}>
        <p className={styles.welcome}>
          Welcome, <span className={styles.highlight}>{userName}</span>!
        </p>
        <p className={styles.course}>
          You are in: <span className={styles.highlight}>{courseName}</span>
        </p>
      </div>
      
      {showTime && (
        <div className={styles.time}>
          <p>Current time: {formatTime(currentTime)}</p>
        </div>
      )}
      
      <div className={styles.interactive}>
        <button 
          className={styles.button}
          onClick={handleClick}
        >
          Click me!
        </button>
        <p className={styles.counter}>
          Button clicked: <span className={styles.count}>{clickCount}</span> times
        </p>
      </div>
      
      <div className={styles.footer}>
        <p className={styles.powered}>Powered by React {React.version}</p>
      </div>
    </div>
  )
}

export default HelloMoodle