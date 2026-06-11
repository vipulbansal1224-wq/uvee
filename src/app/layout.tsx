import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "UVEE | Premium Roasted Nuts & Spices",
  description: "Experience the finest quality roasted nuts, seeds, and spices.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body>
        {children}
      </body>
    </html>
  );
}
