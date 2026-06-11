import styles from './ProductCard.module.css';
import Link from 'next/link';

export default function ProductCard({ product }) {
  return (
    <div className={styles.card}>
      <Link href={`/product/${product.id}`} className={styles.imageLink}>
        <div className={styles.imageWrapper}>
          <img 
            src={product.image} 
            alt={product.name} 
            className={styles.image}
            loading="lazy"
          />
          <div className={styles.overlay}>
            <span className={styles.quickView}>Quick View</span>
          </div>
        </div>
      </Link>
      
      <div className={styles.info}>
        <span className={styles.category}>{product.category}</span>
        <Link href={`/product/${product.id}`}>
          <h3 className={styles.title}>{product.name}</h3>
        </Link>
        <div className={styles.bottomRow}>
          <span className={styles.price}>₹{product.price.toFixed(2)}</span>
          <button className={styles.addToCart} aria-label="Add to Cart">
            +
          </button>
        </div>
      </div>
    </div>
  );
}
