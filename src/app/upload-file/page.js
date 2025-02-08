"use client";
import { useState } from "react";

export default function UploadPage() {
  const [file, setFile] = useState(null);
  const [progress, setProgress] = useState(0);
  const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB per chunk

  const handleFileChange = (event) => {
    setFile(event.target.files[0]);
  };

  const uploadChunks = async () => {
    if (!file) return alert("Select a file first!");

    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    let uploadedSize = 0;

    for (let i = 0; i < totalChunks; i++) {
      const chunk = file.slice(i * CHUNK_SIZE, (i + 1) * CHUNK_SIZE);
      const formData = new FormData();
      formData.append("file", chunk);
      formData.append("fileName", file.name);
      formData.append("chunkIndex", i);
      formData.append("totalChunks", totalChunks);

      await fetch("http://127.0.0.1:8000/api/file/upload-chunk", {
        method: "POST",
        body: formData,
      });

      uploadedSize += chunk.size;
      setProgress(Math.round((uploadedSize / file.size) * 100));
    }

    alert("Upload completed!");
  };

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold mb-4">Upload Large Files</h1>
      <input
        type="file"
        onChange={handleFileChange}
        className="mb-2 border p-2"
      />
      <button
        onClick={uploadChunks}
        className="bg-blue-500 text-white px-4 py-2 rounded"
      >
        Upload
      </button>
      <div className="mt-4">
        <p>Upload Progress: {progress}%</p>
        <div className="w-full bg-gray-200 rounded h-2">
          <div
            className="bg-blue-600 h-2"
            style={{ width: `${progress}%` }}
          ></div>
        </div>
      </div>
    </div>
  );
}
