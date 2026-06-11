'use client';
import { useState, useEffect } from 'react';
import styles from './ImageSlider.module.css';

const sliderImages = [
  '/wp-content/uploads/2022/07/roasted-masala-cashew-1.jpeg',
  '/wp-content/uploads/2022/08/WhatsApp-Image-2022-08-20-at-7.49.19-PM-2.jpeg',
  '/wp-content/uploads/2022/07/salted-almond-1.jpeg',
  '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.16-PM-1.jpeg'
];

export default function ImageSlider({ images }) {
  const finalImages = images && images.length > 0 ? images : sliderImages;
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentIndex((prevIndex) => (prevIndex + 1) % finalImages.length);
    }, 4000);
    return () => clearInterval(timer);
  }, []);

  return (
    <div className={styles.sliderContainer}>
      {finalImages.map((image, index) => (
        <img
          key={index}
          src={image}
          alt={`Slide ${index}`}
          className={`${styles.slide} ${index === currentIndex ? styles.active : ''}`}
        />
      ))}
      <div className={styles.dots}>
        {finalImages.map((_, index) => (
          <span
            key={index}
            className={`${styles.dot} ${index === currentIndex ? styles.activeDot : ''}`}
            onClick={() => setCurrentIndex(index)}
          />
        ))}
      </div>
    </div>
  );
}
