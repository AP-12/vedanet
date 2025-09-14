import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
  return (
    <footer className="bg-gray-900 text-gray-400 py-12">
      <div className="container mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div className="md:col-span-1">
            <Link to="/" className="flex items-center mb-4">
              <img 
                src="/logo.png" 
                alt="Vedanet Solutions Logo" 
                className="h-20 w-auto object-contain"
              />
            </Link>
            <p className="text-sm">Crafting innovative digital experiences for a modern world.</p>
          </div>
          
          <div>
            <h4 className="font-bold text-white text-lg mb-4">Quick Links</h4>
            <ul className="space-y-2">
              <li>
                <Link to="/" className="hover:text-white transition-colors">Home</Link>
              </li>
              <li>
                <Link to="/about" className="hover:text-white transition-colors">About Us</Link>
              </li>
              <li>
                <Link to="/projects" className="hover:text-white transition-colors">Portfolio</Link>
              </li>
              <li>
                <Link to="/contact" className="hover:text-white transition-colors">Contact</Link>
              </li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-bold text-white text-lg mb-4">Services</h4>
            <ul className="space-y-2">
              <li>
                <span className="hover:text-white transition-colors cursor-pointer">Web Development</span>
              </li>
              <li>
                <span className="hover:text-white transition-colors cursor-pointer">Mobile Apps</span>
              </li>
              <li>
                <span className="hover:text-white transition-colors cursor-pointer">UI/UX Design</span>
              </li>
              <li>
                <span className="hover:text-white transition-colors cursor-pointer">Fintech Solutions</span>
              </li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-bold text-white text-lg mb-4">Connect With Us</h4>
            <div className="flex space-x-4">
              <a className="text-gray-400 hover:text-white transition-colors" href="#">
                <svg aria-hidden="true" className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path clipRule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" fillRule="evenodd"></path>
                </svg>
              </a>
              <a className="text-gray-400 hover:text-white transition-colors" href="#">
                <svg aria-hidden="true" className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                </svg>
              </a>
              <a className="text-gray-400 hover:text-white transition-colors" href="#">
                <svg aria-hidden="true" className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path clipRule="evenodd" d="M12 2C6.477 2 2 6.477 2 12.011c0 4.418 2.865 8.169 6.838 9.495.5.092.682-.217.682-.482 0-.237-.009-.868-.014-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.031-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.03 1.595 1.03 2.688 0 3.848-2.338 4.695-4.566 4.942.359.308.678.92.678 1.853 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.001 10.001 0 0022 12.011C22 6.477 17.523 2 12 2z" fillRule="evenodd"></path>
                </svg>
              </a>
            </div>
          </div>
        </div>
        
        <div className="mt-8 border-t border-gray-800 pt-8 text-center text-sm">
          <p>© 2025 Vedanet Solutions Pvt Ltd. All Rights Reserved.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;