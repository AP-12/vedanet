import React, { useState, useEffect } from 'react';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import emailjs from '@emailjs/browser';
import 'leaflet/dist/leaflet.css';

// Fix for default markers in react-leaflet
import L from 'leaflet';
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

const ContactEmailJS = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    company: '',
    address: '',
    message: ''
  });

  const [formStatus, setFormStatus] = useState({
    submitting: false,
    submitted: false,
    error: null
  });

  const [currentLocation, setCurrentLocation] = useState({
    address: 'Mohali, Punjab, India',
    email: 'tusharsharma.vedanet@gmail.com',
    coordinates: { latitude: 30.7046, longitude: 76.7179 },
    loading: false,
    error: null
  });

  // EmailJS configuration - Replace with your actual values
  const EMAILJS_SERVICE_ID = 'YOUR_SERVICE_ID';
  const EMAILJS_TEMPLATE_ID = 'YOUR_TEMPLATE_ID';
  const EMAILJS_PUBLIC_KEY = 'YOUR_PUBLIC_KEY';

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const validateForm = () => {
    const errors = {};

    if (!formData.name.trim()) {
      errors.name = 'Name is required';
    }

    if (!formData.email.trim()) {
      errors.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      errors.email = 'Email is invalid';
    }

    if (!formData.message.trim()) {
      errors.message = 'Message is required';
    }

    return errors;
  };

  const sendEmail = async (formData) => {
    // Initialize EmailJS
    emailjs.init(EMAILJS_PUBLIC_KEY);

    const templateParams = {
      from_name: formData.name,
      from_email: formData.email,
      company: formData.company || 'Not provided',
      address: formData.address || 'Not provided',
      message: formData.message,
      to_email: 'tusharsharma.vedanet@gmail.com'
    };

    try {
      const response = await emailjs.send(
        EMAILJS_SERVICE_ID,
        EMAILJS_TEMPLATE_ID,
        templateParams
      );
      
      console.log('Email sent successfully:', response);
      return { success: true, message: 'Email sent successfully!' };
    } catch (error) {
      console.error('Email sending failed:', error);
      throw new Error('Failed to send email. Please try again later.');
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const errors = validateForm();
    if (Object.keys(errors).length > 0) {
      setFormStatus({
        submitting: false,
        submitted: false,
        error: 'Please fill in all required fields correctly.'
      });
      return;
    }

    setFormStatus({ submitting: true, submitted: false, error: null });

    try {
      await sendEmail(formData);

      console.log('Form submitted successfully:', formData);

      setFormStatus({
        submitting: false,
        submitted: true,
        error: null
      });

      // Reset form after successful submission
      setFormData({ name: '', email: '', company: '', address: '', message: '' });

      // Reset success message after 5 seconds
      setTimeout(() => {
        setFormStatus({ submitting: false, submitted: false, error: null });
      }, 5000);

    } catch (error) {
      console.error('Form submission error:', error);
      setFormStatus({
        submitting: false,
        submitted: false,
        error: error.message || 'Failed to send message. Please try again later.'
      });
    }
  };

  const fetchCurrentLocation = async () => {
    setCurrentLocation({
      address: 'Mohali, Punjab, India',
      email: 'tusharsharma.vedanet@gmail.com',
      coordinates: { latitude: 30.7046, longitude: 76.7179 },
      loading: false,
      error: null
    });
  };

  useEffect(() => {
    fetchCurrentLocation();
  }, []);

  const handlePickLocation = () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const { latitude, longitude } = position.coords;
          const locationString = `Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}`;
          setFormData({
            ...formData,
            address: locationString
          });
        },
        (error) => {
          console.error('Error getting location:', error);
          alert('Unable to retrieve your location. Please check your browser settings and try again.');
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 300000
        }
      );
    } else {
      alert('Geolocation is not supported by this browser.');
    }
  };

  return (
    <>
      {/* Hero Section */}
      <main className="py-16 px-4">
        <div className="container mx-auto">
          <div className="text-center mb-12">
            <h1 className="text-4xl font-bold text-gray-800 tracking-tight">Contact Us</h1>
            <p className="mt-2 text-xl text-blue-600 font-medium typing-animation">Think Smart, Think Vedanet</p>
            <p className="mt-4 text-lg text-gray-600">Get in touch with us to discuss your project and how we can help you succeed.</p>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {/* Contact Information */}
            <div>
              <h2 className="text-3xl font-bold text-gray-800 mb-8">Get in Touch</h2>
              
              <div className="space-y-6 mb-8">
                <div className="flex items-start gap-4">
                  <div className="flex items-center justify-center rounded-full bg-blue-100 p-3">
                    <span className="material-symbols-outlined text-blue-500 text-2xl">location_on</span>
                  </div>
                  <div>
                    <h3 className="text-lg font-semibold text-gray-800 mb-1">Address</h3>
                    {currentLocation.loading ? (
                      <p className="text-gray-600">Loading current location...</p>
                    ) : currentLocation.error ? (
                      <p className="text-red-600">{currentLocation.error}</p>
                    ) : (
                      <p className="text-gray-600" style={{ whiteSpace: 'pre-line' }}>{currentLocation.address}</p>
                    )}
                  </div>
                </div>
                
                <div className="flex items-start gap-4">
                  <div className="flex items-center justify-center rounded-full bg-blue-100 p-3">
                    <span className="material-symbols-outlined text-blue-500 text-2xl">email</span>
                  </div>
                  <div>
                    <h3 className="text-lg font-semibold text-gray-800 mb-1">Email</h3>
                    <p className="text-gray-600">{currentLocation.email}</p>
                  </div>
                </div>
                
                <div className="flex items-start gap-4">
                  <div className="flex items-center justify-center rounded-full bg-blue-100 p-3">
                    <span className="material-symbols-outlined text-blue-500 text-2xl">schedule</span>
                  </div>
                  <div>
                    <h3 className="text-lg font-semibold text-gray-800 mb-1">Business Hours</h3>
                    <p className="text-gray-600">Monday - Friday: 9:00 AM - 6:00 PM<br />Saturday: 10:00 AM - 4:00 PM</p>
                  </div>
                </div>
              </div>
              
              {/* Social Links */}
              <div>
                <h3 className="text-lg font-semibold text-gray-800 mb-4">Follow Us</h3>
                <div className="flex gap-4">
                  <button className="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors" title="Twitter">
                    <svg className="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                    </svg>
                  </button>
                  <button className="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors" title="Facebook">
                    <svg className="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                    </svg>
                  </button>
                  <button className="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors" title="LinkedIn">
                    <svg className="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            
            {/* Contact Form */}
            <div>
              <div className="bg-white rounded-xl border border-gray-200 p-8 shadow-sm">
                <h2 className="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h2>
                
                <form onSubmit={handleSubmit} className="space-y-6">
                  {/* Form Status Messages */}
                  {formStatus.error && (
                    <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                      <div className="flex items-center">
                        <span className="material-symbols-outlined text-red-500 mr-2">error</span>
                        <p className="text-red-700 text-sm">{formStatus.error}</p>
                      </div>
                    </div>
                  )}

                  {formStatus.submitted && (
                    <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                      <div className="flex items-center">
                        <span className="material-symbols-outlined text-green-500 mr-2">check_circle</span>
                        <p className="text-green-700 text-sm">Thank you! Your message has been sent successfully. We'll get back to you soon.</p>
                      </div>
                    </div>
                  )}

                  <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">
                      Full Name *
                    </label>
                    <input
                      type="text"
                      id="name"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      required
                      disabled={formStatus.submitting}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed"
                      placeholder="Enter your full name"
                    />
                  </div>
                  
                  <div>
                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                      Email Address *
                    </label>
                    <input
                      type="email"
                      id="email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      required
                      disabled={formStatus.submitting}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed"
                      placeholder="Enter your email address"
                    />
                  </div>
                  
                  <div>
                    <label htmlFor="company" className="block text-sm font-medium text-gray-700 mb-2">
                      Company
                    </label>
                    <input
                      type="text"
                      id="company"
                      name="company"
                      value={formData.company}
                      onChange={handleChange}
                      disabled={formStatus.submitting}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed"
                      placeholder="Enter your company name"
                    />
                  </div>

                  <div>
                    <label htmlFor="address" className="block text-sm font-medium text-gray-700 mb-2">
                      Address
                    </label>
                    <div className="flex gap-2">
                      <input
                        type="text"
                        id="address"
                        name="address"
                        value={formData.address}
                        onChange={handleChange}
                        disabled={formStatus.submitting}
                        className="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed"
                        placeholder="Enter your address or use location picker"
                      />
                      <button
                        type="button"
                        onClick={handlePickLocation}
                        disabled={formStatus.submitting}
                        className="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed"
                        title="Pick your current location"
                      >
                        📍
                      </button>
                    </div>
                  </div>
                  
                  <div>
                    <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-2">
                      Message *
                    </label>
                    <textarea
                      id="message"
                      name="message"
                      value={formData.message}
                      onChange={handleChange}
                      required
                      disabled={formStatus.submitting}
                      rows={6}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none disabled:bg-gray-100 disabled:cursor-not-allowed"
                      placeholder="Tell us about your project or how we can help you"
                    />
                  </div>
                  
                  <button
                    type="submit"
                    disabled={formStatus.submitting}
                    className="w-full bg-blue-500 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-600 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center"
                  >
                    {formStatus.submitting ? (
                      <>
                        <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        Sending...
                      </>
                    ) : (
                      'Send Message'
                    )}
                  </button>
                </form>
              </div>
            </div>
          </div>
          
          {/* Map Section */}
          <div className="mt-16">
            <h2 className="text-3xl font-bold text-gray-800 mb-8 text-center">Find Us</h2>
            <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
              {currentLocation.loading ? (
                <div className="h-96 flex items-center justify-center bg-gray-100 rounded-lg">
                  <div className="text-center">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                    <p className="text-gray-600">Loading your location...</p>
                  </div>
                </div>
              ) : currentLocation.error ? (
                <div className="h-96 flex items-center justify-center bg-red-50 rounded-lg">
                  <div className="text-center text-red-600">
                    <span className="material-symbols-outlined text-6xl mb-4">error</span>
                    <p className="text-lg">{currentLocation.error}</p>
                    <p className="text-sm mt-2">Please enable location services and refresh the page.</p>
                  </div>
                </div>
              ) : currentLocation.coordinates ? (
                <div className="h-96 rounded-lg overflow-hidden">
                  <MapContainer
                    center={[currentLocation.coordinates.latitude, currentLocation.coordinates.longitude]}
                    zoom={15}
                    style={{ height: '100%', width: '100%' }}
                    className="rounded-lg"
                  >
                    <TileLayer
                      url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                      attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    />
                    <Marker position={[currentLocation.coordinates.latitude, currentLocation.coordinates.longitude]}>
                      <Popup>
                        <div className="text-center">
                          <h3 className="font-semibold">Our Location</h3>
                          <p className="text-sm text-gray-600 mt-1" style={{ whiteSpace: 'pre-line' }}>
                            {currentLocation.address}
                          </p>
                          <p className="text-xs text-gray-500 mt-2">
                            Lat: {currentLocation.coordinates.latitude.toFixed(6)}<br />
                            Lng: {currentLocation.coordinates.longitude.toFixed(6)}
                          </p>
                        </div>
                      </Popup>
                    </Marker>
                  </MapContainer>
                </div>
              ) : (
                <div className="h-96 flex items-center justify-center bg-gray-100 rounded-lg">
                  <div className="text-center text-gray-600">
                    <span className="material-symbols-outlined text-6xl mb-4">location_off</span>
                    <p className="text-lg">Location not available</p>
                    <p className="text-sm">Please check your browser settings and refresh the page.</p>
                  </div>
              </div>
              )}
            </div>
          </div>
        </div>
      </main>
    </>
  );
};

export default ContactEmailJS;
