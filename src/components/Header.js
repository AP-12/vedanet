import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';

const Header = () => {
  const location = useLocation();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [logoKey, setLogoKey] = useState(0);

  useEffect(() => {
    setLogoKey(prev => prev + 1);
  }, [location.pathname]);

  const isActive = (path) => {
    return location.pathname === path;
  };

  return (
     <header className="bg-white fixed top-0 left-0 right-0 z-50">
       <div className="container mx-auto px-4">
         <div className="flex justify-between items-center py-1.5">
           <Link to="/" className="flex flex-col items-center group">
             <img 
               key={logoKey}
               src="/logo.png" 
               alt="Vedanet Solutions Logo" 
               className="w-32 h-32 rounded-full object-cover p-0 m-0 overflow-hidden transition-all duration-500 hover:scale-110 hover:rotate-6 group-hover:shadow-lg animate-rotateIn"
               style={{ objectPosition: 'center' }}
             />
             <p className="mt-2 text-sm text-blue-600 font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300 typing-animation">
               Think Smart, Think Vedanet
             </p>
           </Link>
          
          <nav className="hidden md:flex items-center gap-8 text-gray-600 font-medium">
            <Link 
              to="/" 
              className={`transition-colors duration-300 ${
                isActive('/') 
                  ? 'text-blue-500 font-semibold' 
                  : 'hover:text-blue-500'
              }`}
            >
              Home
            </Link>
            <Link 
              to="/about" 
              className={`transition-colors duration-300 ${
                isActive('/about') 
                  ? 'text-blue-500 font-semibold' 
                  : 'hover:text-blue-500'
              }`}
            >
              About Us
            </Link>
            <Link 
              to="/projects" 
              className={`transition-colors duration-300 ${
                isActive('/projects') 
                  ? 'text-blue-500 font-semibold' 
                  : 'hover:text-blue-500'
              }`}
            >
              Portfolio
            </Link>
            <Link 
              to="/contact" 
              className={`transition-colors duration-300 ${
                isActive('/contact') 
                  ? 'text-blue-500 font-semibold' 
                  : 'hover:text-blue-500'
              }`}
            >
              Contact Us
            </Link>
          </nav>
          
          <Link 
            to="/contact" 
            className="hidden md:inline-block bg-blue-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors"
          >
            Get a Quote
          </Link>
          
          <button 
            className="md:hidden flex items-center justify-center rounded-lg h-12 bg-transparent text-gray-900 gap-2 p-0"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
          >
            <span className="material-symbols-outlined text-gray-900">menu</span>
          </button>
        </div>
        
        {/* Mobile Menu */}
        {isMenuOpen && (
          <div className="md:hidden py-4 border-t border-gray-200">
            <nav className="flex flex-col space-y-4">
              <Link 
                to="/" 
                className={`transition-colors duration-300 ${
                  isActive('/') 
                    ? 'text-blue-500 font-semibold' 
                    : 'text-gray-600 hover:text-blue-500'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Home
              </Link>
              <Link 
                to="/about" 
                className={`transition-colors duration-300 ${
                  isActive('/about') 
                    ? 'text-blue-500 font-semibold' 
                    : 'text-gray-600 hover:text-blue-500'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                About Us
              </Link>
              <Link 
                to="/projects" 
                className={`transition-colors duration-300 ${
                  isActive('/projects') 
                    ? 'text-blue-500 font-semibold' 
                    : 'text-gray-600 hover:text-blue-500'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Portfolio
              </Link>
              <Link 
                to="/contact" 
                className={`transition-colors duration-300 ${
                  isActive('/contact') 
                    ? 'text-blue-500 font-semibold' 
                    : 'text-gray-600 hover:text-blue-500'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Contact Us
              </Link>
              <Link 
                to="/contact" 
                className="mt-4 bg-blue-500 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-600 transition-colors text-center"
                onClick={() => setIsMenuOpen(false)}
              >
                Get a Quote
              </Link>
            </nav>
          </div>
        )}
      </div>
    </header>
  );
};

export default Header;