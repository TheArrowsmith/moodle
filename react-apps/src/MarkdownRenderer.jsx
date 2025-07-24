import React, { useEffect, useRef } from 'react';
import hljs from 'highlight.js';
import 'highlight.js/styles/github.css'; // Default theme
import styles from './MarkdownRenderer.module.css';

const MarkdownRenderer = ({ htmlContent, theme = 'github' }) => {
  const containerRef = useRef(null);

  useEffect(() => {
    if (!containerRef.current) return;

    // Find all code blocks
    const codeBlocks = containerRef.current.querySelectorAll('pre code');
    
    codeBlocks.forEach((block) => {
      // Apply syntax highlighting
      hljs.highlightElement(block);
      
      // Add copy button
      const copyButton = document.createElement('button');
      copyButton.className = styles.copyButton;
      copyButton.innerHTML = '📋 Copy';
      copyButton.onclick = () => copyCodeToClipboard(block, copyButton);
      
      // Wrap in container for positioning
      const wrapper = document.createElement('div');
      wrapper.className = styles.codeBlockWrapper;
      block.parentNode.insertBefore(wrapper, block.parentNode);
      wrapper.appendChild(block.parentNode);
      wrapper.appendChild(copyButton);
    });
  }, [htmlContent]);

  const copyCodeToClipboard = async (codeBlock, button) => {
    try {
      await navigator.clipboard.writeText(codeBlock.textContent);
      button.innerHTML = '✅ Copied!';
      setTimeout(() => {
        button.innerHTML = '📋 Copy';
      }, 2000);
    } catch (err) {
      console.error('Failed to copy code:', err);
      button.innerHTML = '❌ Failed';
      setTimeout(() => {
        button.innerHTML = '📋 Copy';
      }, 2000);
    }
  };

  return (
    <div 
      ref={containerRef}
      className={styles.markdownContent}
      dangerouslySetInnerHTML={{ __html: htmlContent }}
    />
  );
};

export default MarkdownRenderer;