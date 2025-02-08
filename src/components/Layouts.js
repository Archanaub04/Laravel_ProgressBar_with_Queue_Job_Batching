import Link from "next/link";

export default function Layouts({ children }) {
  return (
    <div>
      <Link href="/" className="mx-2 font-semibold">
        Home
      </Link>
      <Link href="/upload" className="mx-2 font-semibold">
        Excel Upload
      </Link>
      <Link href="/upload-file" className="mx-2 font-semibold">
        File Upload
      </Link>
      <div className="flex h-screen">
        <div className="m-auto">{children}</div>
      </div>
    </div>
  );
}
