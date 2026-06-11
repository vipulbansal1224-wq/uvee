import Link from 'next/link';
import styles from './HeroBanner.module.css';

export default function HeroBanner() {
  return (
    <div className={styles.hero}>
      <div className={styles.container}>
        <div className={styles.content}>
          <h1 className={`${styles.title} animate-fade-in`}>
            Premium <span className={styles.highlight}>Nuts & Snacks</span> for a Healthy Life
          </h1>
          <p className={`${styles.subtitle} animate-fade-in delay-100`}>
            Discover our exclusive collection of roasted cashews, salted almonds, and premium fox nuts. Taste the quality in every bite.
          </p>
          <div className={`${styles.actions} animate-fade-in delay-200`}>
            <Link href="/shop" className={styles.primaryBtn}>
              Shop Now
            </Link>
            <Link href="/about" className={styles.secondaryBtn}>
              Our Story
            </Link>
          </div>
        </div>
        <div className={`${styles.imageWrapper} animate-fade-in delay-300`}>
          {/* Using one of their scraped images as a hero showcase */}
          <img 
            src="/wp-content/uploads/2022/08/Makhana-Fox-Nuts-Classic-1.jpeg" 
            alt="Premium Makhana" 
            className={styles.heroImage}
          />
          <div className={styles.floatingCard}>
            <span className={styles.stars}>⭐⭐⭐⭐⭐</span>
            <p>"The best quality almonds I've ever tasted!"</p>
          </div>
        </div>
      </div>
      
      {/* Decorative blobs */}
      <div className={styles.blob1}></div>
      <div className={styles.blob2}></div>
    </div>
  );
}
