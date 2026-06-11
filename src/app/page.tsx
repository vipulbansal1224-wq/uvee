"use client";
import Image from 'next/image';
import { useState, useEffect } from 'react';
import styles from './page.module.css';

const products = [
  { id: 1, name: "Premium Salted Almonds", price: "₹450", image: "/almonds.png" },
  { id: 2, name: "Classic Roasted Makhana", price: "₹250", image: "/makhana.png" },
  { id: 3, name: "Lemon Chilli Spiced Nuts", price: "₹300", image: "/lemon_chilli.png" },
  { id: 4, name: "Golden Roasted Cashews", price: "₹650", image: "/cashews.png" },
];

const banners = [
  "/banner-1.jpg",
  "/banner-2.jpg",
  "/banner-3.jpg"
];

export default function Home() {
  const [currentSlide, setCurrentSlide] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % banners.length);
    }, 5000);
    return () => clearInterval(timer);
  }, []);

  return (
    <>
      <nav className={styles.navbar}>
        <div className={`${styles.navContainer} ${styles.container}`}>
          <div className={styles.logo}>UVEE</div>
          <div className={styles.navLinks}>
            <a href="#" className={styles.navLink}>Home</a>
            <a href="#shop" className={styles.navLink}>Shop</a>
            <a href="#" className={styles.navLink}>About</a>
            <a href="#" className={styles.navLink}>Contact</a>
          </div>
        </div>
      </nav>

      <main className={styles.main}>
        <section className={styles.sliderContainer}>
          {banners.map((banner, index) => (
            <div 
              key={index} 
              className={`${styles.slide} ${index === currentSlide ? styles.active : ''}`}
            >
              <img src={banner} alt={`Banner ${index + 1}`} className={styles.slideImage} />
            </div>
          ))}
        </section>

        <section id="shop" className={styles.section}>
          <div className={styles.container}>
            <h2 className={styles.sectionTitle}>Our Premium Selection</h2>
            <div className={styles.grid}>
              {products.map((product) => (
                <div key={product.id} className={styles.productCard}>
                  <div className={styles.imageWrapper}>
                    <Image 
                      src={product.image} 
                      alt={product.name} 
                      width={300} 
                      height={300} 
                      priority
                    />
                  </div>
                  <div className={styles.productInfo}>
                    <h3 className={styles.productName}>{product.name}</h3>
                    <p className={styles.productPrice}>{product.price}</p>
                    <button className={styles.addToCartBtn}>Add to Cart</button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>
    </>
  );
}
