import React from 'react';
import { useParams, Link } from 'react-router-dom';

const ProjectDetail = () => {
  const { projectId } = useParams();

  const projects = {
    ecommerce: {
      title: "E-Commerce Platform",
      description: "A comprehensive e-commerce solution with advanced features including inventory management, payment processing, and analytics dashboard.",
      image: "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80",
      technologies: ["React", "Node.js", "MongoDB", "Stripe", "AWS"],
      features: [
        "Advanced inventory management system",
        "Secure payment processing with Stripe",
        "Real-time analytics dashboard",
        "Multi-vendor marketplace support",
        "Mobile-responsive design",
        "SEO optimization"
      ],
      challenge: "The client needed a scalable e-commerce platform that could handle thousands of products and multiple vendors while maintaining fast performance.",
      solution: "We built a modern, full-stack solution using React for the frontend, Node.js for the backend, and MongoDB for data storage. The platform includes advanced features like real-time inventory tracking, automated order processing, and comprehensive analytics.",
      results: [
        "40% increase in online sales",
        "60% reduction in order processing time",
        "99.9% uptime achieved",
        "50% improvement in page load speed"
      ]
    },
    healthcare: {
      title: "Healthcare Mobile App",
      description: "A mobile application for healthcare providers with patient management, appointment scheduling, and telemedicine capabilities.",
      image: "https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80",
      technologies: ["React Native", "Firebase", "WebRTC", "Node.js", "MongoDB"],
      features: [
        "Patient management system",
        "Appointment scheduling",
        "Telemedicine video calls",
        "Prescription management",
        "Health records access",
        "Push notifications"
      ],
      challenge: "Healthcare providers needed a secure, HIPAA-compliant mobile solution for managing patients and conducting remote consultations.",
      solution: "We developed a cross-platform mobile app using React Native with secure video calling capabilities, encrypted data storage, and comprehensive patient management features.",
      results: [
        "30% increase in patient engagement",
        "25% reduction in no-show appointments",
        "100% HIPAA compliance achieved",
        "4.8/5 user satisfaction rating"
      ]
    },
    webapp: {
      title: "Modern Web Application",
      description: "A cutting-edge web application built with modern technologies, featuring real-time updates, responsive design, and advanced user interface.",
      image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80",
      technologies: ["React", "TypeScript", "Node.js", "PostgreSQL", "AWS", "Docker"],
      features: [
        "Real-time data synchronization",
        "Responsive design for all devices",
        "Advanced user authentication",
        "Interactive dashboard",
        "API integration",
        "Cloud deployment"
      ],
      challenge: "The client needed a modern, scalable web application that could handle real-time data updates and provide an excellent user experience across all devices.",
      solution: "We built a full-stack application using React with TypeScript for type safety, Node.js for the backend, and PostgreSQL for data storage. The application features real-time updates using WebSockets and is deployed on AWS with Docker containers.",
      results: [
        "50% faster page load times",
        "99.9% uptime achieved",
        "40% increase in user engagement",
        "95% user satisfaction score"
      ]
    }
  };

  const project = projects[projectId];

  if (!project) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-4xl font-bold text-gray-800 mb-4">Project Not Found</h1>
          <Link to="/projects" className="text-blue-500 hover:text-blue-600">
            ← Back to Projects
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <div className="relative h-96 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
        <div className="text-center text-white px-4">
          <h1 className="text-4xl md:text-6xl font-bold mb-4">{project.title}</h1>
          <p className="text-xl opacity-90 max-w-2xl mx-auto">{project.description}</p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-16">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
          {/* Project Image */}
          <div>
            <img 
              src={project.image} 
              alt={project.title}
              className="w-full h-96 object-cover rounded-xl shadow-lg"
            />
          </div>

          {/* Project Details */}
          <div>
            <h2 className="text-3xl font-bold text-gray-800 mb-6">Project Overview</h2>
            
            <div className="mb-8">
              <h3 className="text-xl font-semibold text-gray-800 mb-3">Technologies Used</h3>
              <div className="flex flex-wrap gap-2">
                {project.technologies.map((tech, index) => (
                  <span key={index} className="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">
                    {tech}
                  </span>
                ))}
              </div>
            </div>

            <div className="mb-8">
              <h3 className="text-xl font-semibold text-gray-800 mb-3">Key Features</h3>
              <ul className="space-y-2">
                {project.features.map((feature, index) => (
                  <li key={index} className="flex items-center text-gray-600">
                    <span className="material-symbols-outlined text-green-500 mr-2">check_circle</span>
                    {feature}
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>

        {/* Challenge & Solution */}
        <div className="mt-16 grid grid-cols-1 lg:grid-cols-2 gap-12">
          <div className="bg-white p-8 rounded-xl shadow-sm">
            <h3 className="text-2xl font-bold text-gray-800 mb-4">Challenge</h3>
            <p className="text-gray-600 leading-relaxed">{project.challenge}</p>
          </div>
          
          <div className="bg-white p-8 rounded-xl shadow-sm">
            <h3 className="text-2xl font-bold text-gray-800 mb-4">Solution</h3>
            <p className="text-gray-600 leading-relaxed">{project.solution}</p>
          </div>
        </div>

        {/* Results */}
        <div className="mt-16">
          <h3 className="text-3xl font-bold text-gray-800 mb-8 text-center">Results</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {project.results.map((result, index) => (
              <div key={index} className="bg-white p-6 rounded-xl shadow-sm text-center">
                <div className="text-3xl font-bold text-blue-500 mb-2">
                  {result.split(' ')[0]}
                </div>
                <p className="text-gray-600 text-sm">{result.split(' ').slice(1).join(' ')}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Back Button */}
        <div className="mt-16 text-center">
          <Link 
            to="/projects" 
            className="inline-flex items-center px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition-colors"
          >
            <span className="material-symbols-outlined mr-2">arrow_back</span>
            Back to Projects
          </Link>
        </div>
      </div>
    </div>
  );
};

export default ProjectDetail;
