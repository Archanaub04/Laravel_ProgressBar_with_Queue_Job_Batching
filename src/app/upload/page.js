"use client";

import Layouts from "@/components/Layouts";
import { useEffect, useRef, useState } from "react";

export default function Upload() {
  const [errors, setErrors] = useState([]);
  const fileRef = useRef();
  const API_URL = "http://localhost:8000/api";

  const [batchID, setBatchID] = useState(null);
  const [batchDetail, setBatchDetail] = useState({});
  const [isLoading, setIsLoading] = useState(false);
  const [isCompleted, setIsCompleted] = useState(false);

  function handleForm(e) {
    e.preventDefault();

    if (isLoading) return;

    const inputFile = fileRef.current;
    const file = inputFile.files[0];
    if (!file) return alert("Please select a file.");

    const formData = new FormData();
    formData.append("mycsv", file);

    setIsLoading(true);

    fetch(`${API_URL}/upload`, {
      method: "post",
      body: formData,
    })
      .then((res) => {
        if (!res.ok) {
          return res.json().then((data) => {
            throw data; // Throw error object
          });
        }
        return res.json();
      })
      .then((data) => {
        setBatchID(data.id);
        setBatchDetail(data); // Updated to set the entire batch detail
        setIsLoading(false);
        updateProgress(data.id); // Start progress tracking immediately
      })
      .catch((error) => {
        if (error.errors) {
          setErrors(Object.values(error.errors).flat());
        } else {
          setErrors([error.error || "An error occurred"]);
        }
      });
  }

  const progressInterval = useRef(null);

  function batchDetails(id) {
    fetch(`${API_URL}/batch/${id}`)
      .then((res) => res.json())
      .then((data) => {
        setBatchDetail(data);

        if (data.progress >= 100) {
          clearInterval(progressInterval.current);
          progressInterval.current = null;
          setIsCompleted(true); // Set isCompleted to true
        }
      });
  }

  function updateProgress(id) {
    if (progressInterval.current) return;

    progressInterval.current = setInterval(() => {
      batchDetails(id);
    }, 2000);
  }

  useEffect(() => {
    fetch(`${API_URL}/batch-in-progress`)
      .then((res) => res.json())
      .then((data) => {
        if (data?.id) {
          setBatchID(data.id);
          setBatchDetail(data);
          updateProgress(data.id); // Start progress tracking for in-progress batch
        }
      });
    return () => {
      if (progressInterval.current) clearInterval(progressInterval.current);
    };
  }, []);

  return (
    <Layouts>
      {/* {!batchDetail.progress && ( */}
      <section className="pb-5">
        <h1 className="text-xl text-gray-800 text-center mb-5">
          Choose an Excel file to Upload
        </h1>
        <form className="border rounded p-4" onSubmit={handleForm}>
          <input type="file" ref={fileRef} name="mycsv" id="mycsv" />
          <input
            type="submit"
            value="Upload"
            className={`px-4 py-2 rounded text-white 
              ${isLoading ? "bg-gray-400 outline-none" : "bg-gray-700"}`}
          />
        </form>

        {errors.length > 0 && (
          <div style={{ color: "red" }}>
            {errors.map((err, idx) => (
              <p key={idx}>{err}</p>
            ))}
          </div>
        )}
      </section>
      {/* )} */}

      {batchDetail.progress != undefined && !isCompleted && (
        <section className="text-center">
          <p>Upload is in progress ({batchDetail.progress}% complete)</p>
          <div className="w-full h-4 rounded-lg shadow-inner border">
            <div
              className="bg-blue-700 w-full h-4 rounded-lg"
              style={{ width: `${batchDetail.progress}%` }}
            ></div>
          </div>
        </section>
      )}

      {isCompleted && (
        <section className="text-center">
          <p>Upload Completed!</p>
        </section>
      )}
    </Layouts>
  );
}
