import './globals.css';

export const metadata = {
  title: 'UVEE - Premium Dry Fruits & Snacks',
  description: 'Premium quality roasted cashews, salted almonds, and fox nuts.',
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
