import React from 'react';
import { Link } from 'react-router-dom';

const Projects = () => {
  return (
    <>
      {/* Hero Section */}
      <main className="py-16 px-4">
        <div className="container mx-auto">
          <div className="text-center mb-12">
            <h1 className="text-4xl font-bold text-gray-800 tracking-tight">Our Portfolio</h1>
            <p className="mt-2 text-xl text-blue-600 font-medium typing-animation">Think Smart, Think Vedanet</p>
            <p className="mt-4 text-lg text-gray-600">Explore our recent projects and see how we bring ideas to life through innovative technology.</p>
          </div>
          
          {/* Featured Projects */}
          <div className="mb-16">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">Featured Projects</h2>
              <p className="text-lg text-gray-600">Some of our most impactful and innovative solutions</p>
            </div>
            
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
              <div className="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="h-64 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-6xl">shopping_cart</span>
                </div>
                <div className="p-6">
                  <h3 className="text-2xl font-bold text-gray-800 mb-3">E-Commerce Platform</h3>
                  <p className="text-gray-600 leading-relaxed mb-4">
                    A comprehensive e-commerce solution with advanced features including inventory management, 
                    payment processing, and analytics dashboard.
                  </p>
                  <div className="flex flex-wrap gap-2 mb-4">
                    <span className="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">React</span>
                    <span className="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">Node.js</span>
                    <span className="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">MongoDB</span>
                  </div>
                  <Link to="/projects/ecommerce" className="text-blue-500 font-semibold hover:text-blue-600 transition-colors">
                    View Details →
                  </Link>
                </div>
              </div>
              
              <div className="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="h-64 bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-6xl">health_and_safety</span>
                </div>
                <div className="p-6">
                  <h3 className="text-2xl font-bold text-gray-800 mb-3">Healthcare Mobile App</h3>
                  <p className="text-gray-600 leading-relaxed mb-4">
                    A mobile application for healthcare providers with patient management, appointment scheduling, 
                    and telemedicine capabilities.
                  </p>
                  <div className="flex flex-wrap gap-2 mb-4">
                    <span className="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">React Native</span>
                    <span className="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">Firebase</span>
                    <span className="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">WebRTC</span>
                  </div>
                  <Link to="/projects/healthcare" className="text-blue-500 font-semibold hover:text-blue-600 transition-colors">
                    View Details →
                  </Link>
                </div>
              </div>
            </div>
          </div>

          {/* All Projects Grid */}
          <div className="mb-16">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">All Projects</h2>
              <p className="text-lg text-gray-600">A comprehensive view of our work across different industries and technologies</p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {/* Patent Services */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">science</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Patent Services</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Electrical, electronics, and networking patent research, documentation, and filing assistance.
                  </p>
                </div>
              </div>
              
              {/* OpenAI Integration */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">psychology</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">OpenAI Integration</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    AI-powered solutions using OpenAI APIs for chatbots, content generation, and automation.
                  </p>
                </div>
              </div>
              
              {/* WordPress Development */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">web</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">WordPress Development</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Custom WordPress themes, plugins, and full website development with modern design.
                  </p>
                </div>
              </div>
              
              {/* Shopify Stores */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">store</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Shopify E-commerce</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Complete Shopify store setup, customization, and optimization for online businesses.
                  </p>
                </div>
              </div>
              
              {/* Logo Design */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">palette</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Logo Design</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Professional logo design and brand identity creation for businesses and startups.
                  </p>
                </div>
              </div>
              
              {/* Banking System */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">account_balance</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Banking System</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Secure online banking platform with multi-factor authentication and real-time transaction processing.
                  </p>
                </div>
              </div>
              
              {/* Learning Management System */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">school</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Learning Management System</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Comprehensive educational platform with course management, student tracking, and interactive content.
                  </p>
                </div>
              </div>
              
              {/* Inventory Management */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">inventory</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Inventory Management</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Advanced inventory tracking system with automated reordering and analytics dashboard.
                  </p>
                </div>
              </div>
              
              {/* Restaurant POS System */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">restaurant</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Restaurant POS System</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Point-of-sale system with table management, order tracking, and integrated payment processing.
                  </p>
                </div>
              </div>
              
              {/* Modern Web Application */}
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">web</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Modern Web Application</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    Cutting-edge web application with real-time updates, responsive design, and advanced UI.
                  </p>
                  <Link to="/projects/webapp" className="text-blue-500 font-semibold hover:text-blue-600 transition-colors mt-2">
                    View Details →
                  </Link>
                </div>
              </div>
            </div>
          </div>

          {/* Technology Stack */}
          <div className="mb-16">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">Technologies We Use</h2>
              <p className="text-lg text-gray-600">Modern tools and frameworks for building exceptional solutions</p>
            </div>
            
            <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
              {[
                'React', 'Node.js', 'Python', 'JavaScript', 'TypeScript', 'MongoDB',
                'PostgreSQL', 'AWS', 'Docker', 'Kubernetes', 'GraphQL', 'Redis',
                'WordPress', 'Shopify', 'OpenAI', 'PHP', 'MySQL', 'Figma', 'Flutter'
              ].map((tech, index) => (
                <div key={index} className="flex flex-col items-center gap-3 p-4 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                  <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span className="text-white font-bold text-sm">{tech.charAt(0)}</span>
                  </div>
                  <span className="text-sm font-medium text-gray-700">{tech}</span>
                </div>
              ))}
            </div>
          </div>

          {/* CTA Section */}
          <div className="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-8 text-white text-center">
            <h2 className="text-3xl font-bold mb-4">Ready to Start Your Project?</h2>
            <p className="text-lg mb-6 opacity-90">
              Let's discuss how we can bring your ideas to life with innovative technology solutions.
            </p>
            <button className="bg-white text-blue-500 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition-colors">
              Get Started Today
            </button>
          </div>
        </div>
      </main>
    </>
  );
};

export default Projects;