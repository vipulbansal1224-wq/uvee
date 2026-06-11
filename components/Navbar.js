import Link from 'next/link';
import styles from './Navbar.module.css';

export default function Navbar() {
  return (
    <nav className={styles.navbar}>
      <div className={styles.container}>
        <Link href="/" className={styles.logo}>
          <img src="/wp-content/uploads/2022/07/logo-1.jpg" alt="UVEE Logo" style={{ height: '40px', width: 'auto' }} />
        </Link>
        <div className={styles.navLinks}>
          <Link href="/" className={styles.link}>Home</Link>
          <Link href="/shop" className={styles.link}>Shop</Link>
          <Link href="/about" className={styles.link}>About Us</Link>
          <Link href="/contact" className={styles.link}>Contact</Link>
        </div>
        <div className={styles.actions}>
          <Link href="/cart" className={styles.cartBtn}>
            🛒 Cart (0)
          </Link>
        </div>
      </div>
    </nav>
  );
}
