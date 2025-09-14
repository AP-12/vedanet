import React from 'react';

const About = () => {
  return (
    <>
      {/* Hero Section */}
      <main className="py-16 px-4">
        <div className="container mx-auto">
          <div className="text-center mb-12">
            <h1 className="text-4xl font-bold text-gray-800 tracking-tight">About Us</h1>
            <p className="mt-2 text-xl text-blue-600 font-medium typing-animation">Think Smart, Think Vedanet</p>
            <p className="mt-4 text-lg text-gray-600">Learn more about our mission, vision, and the team behind Vedanet Solutions Pvt Ltd.</p>
          </div>
          
          {/* Mission Section */}
          <div className="mb-16">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
              <div>
                <h2 className="text-3xl font-bold text-gray-800 mb-6">Our Mission</h2>
                <p className="text-lg text-gray-600 leading-relaxed mb-6">
                  At Vedanet Solutions Pvt Ltd, we are dedicated to transforming businesses through innovative technology solutions. 
                  Our mission is to bridge the gap between complex technology and practical business needs, delivering 
                  solutions that drive growth, efficiency, and success.
                </p>
                <p className="text-lg text-gray-600 leading-relaxed">
                  We believe in the power of technology to solve real-world problems and create meaningful impact. 
                  Every project we undertake is approached with passion, precision, and a commitment to excellence.
                </p>
              </div>
              <div className="flex items-center justify-center">
                <div className="w-full h-80 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-6xl">rocket_launch</span>
                </div>
              </div>
            </div>
          </div>

          {/* Values Section */}
          <div className="mb-16">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">Our Core Values</h2>
              <p className="text-lg text-gray-600">The principles that guide everything we do</p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">lightbulb</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Innovation</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    We constantly explore new technologies and methodologies to deliver cutting-edge solutions.
                  </p>
                </div>
              </div>
              
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">verified</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Quality</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    We maintain the highest standards in every project, ensuring reliable and robust solutions.
                  </p>
                </div>
              </div>
              
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1">
                <div className="flex items-center justify-center rounded-full bg-blue-100 p-4">
                  <span className="material-symbols-outlined text-blue-500 text-3xl">handshake</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-xl font-bold text-gray-900">Partnership</h3>
                  <p className="text-gray-600 text-base leading-relaxed">
                    We work closely with our clients as partners, understanding their unique needs and goals.
                  </p>
                </div>
              </div>
            </div>
            
            {/* Additional Expertise Section */}
            <div className="mt-16">
              <div className="text-center mb-12">
                <h3 className="text-2xl font-bold text-gray-800 mb-4">Our Specialized Expertise</h3>
                <p className="text-lg text-gray-600">Beyond traditional development, we offer specialized services</p>
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="flex flex-col items-center text-center gap-3 p-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                  <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span className="material-symbols-outlined text-white text-xl">science</span>
                  </div>
                  <span className="text-sm font-medium text-gray-700">Patent Services</span>
                </div>
                
                <div className="flex flex-col items-center text-center gap-3 p-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                  <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span className="material-symbols-outlined text-white text-xl">psychology</span>
                  </div>
                  <span className="text-sm font-medium text-gray-700">OpenAI Integration</span>
                </div>
                
                <div className="flex flex-col items-center text-center gap-3 p-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                  <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span className="material-symbols-outlined text-white text-xl">web</span>
                  </div>
                  <span className="text-sm font-medium text-gray-700">WordPress</span>
                </div>
                
                <div className="flex flex-col items-center text-center gap-3 p-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                  <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span className="material-symbols-outlined text-white text-xl">store</span>
                  </div>
                  <span className="text-sm font-medium text-gray-700">Shopify</span>
                </div>
              </div>
            </div>
          </div>

          {/* Story Section */}
          <div className="mb-16">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
              <div className="flex items-center justify-center">
                <div className="w-full h-80 bg-gradient-to-br from-green-500 to-blue-600 rounded-xl flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-6xl">trending_up</span>
                </div>
              </div>
              <div>
                <h2 className="text-3xl font-bold text-gray-800 mb-6">Our Story</h2>
                <p className="text-lg text-gray-600 leading-relaxed mb-6">
                  Founded in 2024, Vedanet Solutions Pvt Ltd began as a small team of passionate developers and designers 
                  who shared a common vision: to make technology accessible and beneficial for businesses of all sizes.
                </p>
                <p className="text-lg text-gray-600 leading-relaxed mb-6">
                  Over the years, we've grown from a startup to a trusted technology partner, working with clients 
                  across various industries. Our journey has been marked by continuous learning, adaptation, and 
                  a relentless pursuit of excellence.
                </p>
                <p className="text-lg text-gray-600 leading-relaxed">
                  Today, we're proud to be at the forefront of digital transformation, helping businesses 
                  leverage technology to achieve their goals and stay competitive in an ever-evolving market.
                </p>
              </div>
            </div>
          </div>

          {/* Leadership Section */}
          <div className="mb-16">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">Our Leadership</h2>
              <p className="text-lg text-gray-600">Meet the visionary leader driving our innovation</p>
            </div>
            
            <div className="flex justify-center">
              <div className="flex flex-col items-center text-center gap-4 rounded-xl border border-gray-200 bg-white p-8 shadow-sm max-w-md">
                <div className="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                  <span className="text-white text-3xl font-bold">TS</span>
                </div>
                <div className="flex flex-col gap-2">
                  <h3 className="text-2xl font-bold text-gray-900">Tushar Sharma</h3>
                  <p className="text-blue-500 font-semibold text-lg">CEO & Founder</p>
                  <p className="text-gray-600 leading-relaxed">
                    Visionary leader driving innovation and strategic growth at Vedanet Solutions Pvt Ltd. 
                    Leading our dynamic team of experts across multiple technology domains.
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Dynamic Team Capabilities */}
          <div className="mb-16">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">Our Dynamic Team</h2>
              <p className="text-lg text-gray-600">A diverse group of experts working across cutting-edge technologies</p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">code</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">MERN Stack</h3>
                <p className="text-sm text-gray-600">Full-stack developers</p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">php</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">Laravel</h3>
                <p className="text-sm text-gray-600">PHP framework experts</p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">terminal</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">Python</h3>
                <p className="text-sm text-gray-600">Backend specialists</p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">psychology</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">OpenAI</h3>
                <p className="text-sm text-gray-600">AI integration experts</p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">design_services</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">UI/UX Design</h3>
                <p className="text-sm text-gray-600">Creative designers</p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">science</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">Patent Research</h3>
                <p className="text-sm text-gray-600">Research analysts</p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-teal-500 to-green-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">analytics</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">Data Analysis</h3>
                <p className="text-sm text-gray-600">Data specialists</p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">web</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">WordPress</h3>
                <p className="text-sm text-gray-600">CMS experts</p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3 p-6 rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div className="w-16 h-16 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-lg flex items-center justify-center">
                  <span className="material-symbols-outlined text-white text-2xl">phone_android</span>
                </div>
                <h3 className="text-lg font-semibold text-gray-800">Flutter</h3>
                <p className="text-sm text-gray-600">Mobile app developers</p>
              </div>
            </div>
          </div>

          {/* Stats Section */}
          <div className="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-8 text-white text-center">
            <h2 className="text-3xl font-bold mb-8">Our Impact in Numbers</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div>
                <div className="text-4xl font-bold mb-2">50+</div>
                <div className="text-lg">Projects Completed</div>
              </div>
              <div>
                <div className="text-4xl font-bold mb-2">30+</div>
                <div className="text-lg">Happy Clients</div>
              </div>
              <div>
                <div className="text-4xl font-bold mb-2">2024</div>
                <div className="text-lg">Founded</div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </>
  );
};

export default About;