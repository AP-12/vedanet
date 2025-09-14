import React from 'react';
import { Link } from 'react-router-dom';

const Home = () => {
  return (
    <>
      {/* Hero Section */}
      <section 
        className="relative min-h-[calc(100vh-72px)] flex items-center justify-center text-center text-white"
        style={{
          backgroundImage: 'linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4)), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDaH0QcEzT72FQ97AFlhvEmYU85Ua0kdF4LijsOBLGpP0bx8AuIctujO7MlfsOt2iqxvT_arAFwTHGE__4odqvMRvtFYH2Z58pZS96Hvj9cfWzUsuOGZt_07MXSjFnbCLkZ8l_rGzfCRVpz3N7-yjhgXAfaNOxOKJc9Zk7R0apx3EJYS70tPCF6zKg5SUWQJqX8uku-OkpQf16PI_iBR4bDWo7-xjxdLe8KJiYU1CQk3440Z0ycXi8U1y0gZOwi5YeijfKXdIDlqsg")',
          backgroundSize: 'cover',
          backgroundPosition: 'center'
        }}
      >
        <div className="max-w-4xl px-4">
          <h2 className="text-4xl md:text-6xl font-black leading-tight tracking-tight animate-slideUp">
            Vedanet Solutions Pvt Ltd
          </h2>
          <p className="mt-2 text-xl md:text-2xl text-blue-300 font-medium animate-fadeIn typing-animation" style={{ animationDelay: '0.3s' }}>
            Think Smart, Think Vedanet
          </p>
          <p className="mt-4 text-lg md:text-xl font-light animate-fadeIn" style={{ animationDelay: '0.6s' }}>
            Crafting Innovative Digital Experiences
          </p>
          <Link 
            to="/projects" 
            className="mt-8 inline-block bg-[#13a4ec] text-white font-bold py-3 px-8 rounded-full hover:bg-[#0f8ac9] transition duration-300"
          >
            Explore Our Services
          </Link>
        </div>
      </section>

      {/* About Section */}
      <section className="py-16 md:py-24 bg-white" id="about">
        <div className="container mx-auto px-4">
          <div className="text-center max-w-3xl mx-auto">
            <h3 className="text-3xl md:text-4xl font-bold mb-4">About Us</h3>
            <p className="text-lg text-gray-600">
              Vedanet Solutions Pvt Ltd is a forward-thinking IT startup dedicated to building exceptional digital products. 
              We partner with businesses to transform their ideas into reality through cutting-edge technology and user-centric design.
            </p>
          </div>
        </div>
      </section>

      {/* Services Section */}
      <section className="py-16 md:py-24 bg-gray-50" id="services">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h3 className="text-3xl md:text-4xl font-bold">Our Expertise</h3>
            <p className="text-lg text-gray-600 mt-2">
              We specialize in a range of IT solutions to propel your business forward.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">code</span>
              <h4 className="text-xl font-bold mb-2">Web & Mobile Development</h4>
              <p className="text-gray-600">Building responsive and scalable web and mobile applications.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">design_services</span>
              <h4 className="text-xl font-bold mb-2">UI/UX Design</h4>
              <p className="text-gray-600">Creating intuitive and engaging user interfaces.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">science</span>
              <h4 className="text-xl font-bold mb-2">Patent Services</h4>
              <p className="text-gray-600">Electrical, electronics, and networking patent research and filing.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">psychology</span>
              <h4 className="text-xl font-bold mb-2">OpenAI Integration</h4>
              <p className="text-gray-600">AI-powered solutions using OpenAI APIs for automation and content.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">web</span>
              <h4 className="text-xl font-bold mb-2">WordPress Development</h4>
              <p className="text-gray-600">Custom WordPress themes, plugins, and full website development.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">store</span>
              <h4 className="text-xl font-bold mb-2">Shopify E-commerce</h4>
              <p className="text-gray-600">Complete Shopify store setup, customization, and optimization.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">palette</span>
              <h4 className="text-xl font-bold mb-2">Logo Design</h4>
              <p className="text-gray-600">Professional logo design and brand identity creation.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">account_balance</span>
              <h4 className="text-xl font-bold mb-2">Fintech Solutions</h4>
              <p className="text-gray-600">Developing secure and efficient financial technology solutions.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
              <span className="material-symbols-outlined text-4xl text-[#13a4ec] mb-4">phone_android</span>
              <h4 className="text-xl font-bold mb-2">Flutter Development</h4>
              <p className="text-gray-600">Cross-platform mobile app development for iOS and Android.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Portfolio Section */}
      <section className="py-16 md:py-24 bg-white" id="portfolio">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h3 className="text-3xl md:text-4xl font-bold">Our Portfolio</h3>
            <p className="text-lg text-gray-600 mt-2">Explore some of our recent projects.</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div className="group">
              <div className="relative overflow-hidden rounded-lg">
                <img 
                  alt="Web Development Project" 
                  className="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300" 
                  src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2015&q=80"
                />
                <div className="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div className="text-center text-white p-4">
                    <h4 className="text-xl font-bold">Web Development</h4>
                    <p>Modern responsive web applications.</p>
                  </div>
                </div>
              </div>
            </div>
            <div className="group">
              <div className="relative overflow-hidden rounded-lg">
                <img 
                  alt="Mobile App Development" 
                  className="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300" 
                  src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                />
                <div className="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div className="text-center text-white p-4">
                    <h4 className="text-xl font-bold">Mobile Apps</h4>
                    <p>Cross-platform mobile applications.</p>
                  </div>
                </div>
              </div>
            </div>
            <div className="group">
              <div className="relative overflow-hidden rounded-lg">
                <img 
                  alt="AI Technology Project" 
                  className="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300" 
                  src="https://images.unsplash.com/photo-1677442136019-21780ecad995?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                />
                <div className="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div className="text-center text-white p-4">
                    <h4 className="text-xl font-bold">AI Solutions</h4>
                    <p>OpenAI integration and automation.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Contact CTA Section */}
      <section className="bg-[#13a4ec] text-white py-16 md:py-24" id="contact">
        <div className="container mx-auto px-4 text-center">
          <h3 className="text-3xl md:text-4xl font-bold">Ready to Transform Your Business?</h3>
          <p className="text-lg mt-4 max-w-2xl mx-auto">
            Contact us today to discuss your project and how we can help you succeed.
          </p>
          <Link 
            to="/contact" 
            className="mt-8 inline-block bg-white text-[#13a4ec] font-bold py-3 px-8 rounded-full hover:bg-gray-200 transition duration-300"
          >
            Get a Free Consultation
          </Link>
        </div>
      </section>
    </>
  );
};

export default Home;