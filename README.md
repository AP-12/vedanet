# Vedanet Solutions Pvt Ltd - React Website

A modern, responsive React website for Vedanet Solutions Pvt Ltd built with React Router and custom CSS styling.

## Features

- 🚀 **Modern React Architecture** - Built with React 18 and React Router
- 🎨 **Beautiful Design** - Custom CSS with gradients, animations, and hover effects
- 📱 **Fully Responsive** - Works perfectly on desktop, tablet, and mobile
- ⚡ **Fast Performance** - Optimized components and efficient routing
- 🎯 **Interactive Elements** - Smooth animations and user-friendly interface

## Pages

- **Home** - Hero section with featured projects and services
- **About** - Company information and expertise showcase
- **Projects** - Portfolio of key projects and live website links
- **Contact** - Contact form with company information

## Getting Started

### Prerequisites

Make sure you have Node.js installed on your system:
- Download from [nodejs.org](https://nodejs.org/)
- Verify installation: `node --version` and `npm --version`

### Installation

1. **Install Dependencies**
   ```bash
   npm install
   ```

2. **Start Development Server**
   ```bash
   npm start
   ```

3. **Open in Browser**
   - The app will automatically open at `http://localhost:3000`
   - If it doesn't open automatically, manually navigate to the URL

### Available Scripts

- `npm start` - Runs the app in development mode
- `npm build` - Builds the app for production
- `npm test` - Launches the test runner
- `npm eject` - Ejects from Create React App (one-way operation)

## Project Structure

```
src/
├── components/
│   ├── Header.js          # Navigation header component
│   └── Footer.js          # Footer component
├── pages/
│   ├── Home.js            # Homepage component
│   ├── About.js           # About page component
│   ├── Projects.js        # Projects page component
│   └── Contact.js         # Contact page component
├── App.js                 # Main app component with routing
├── index.js               # React app entry point
└── index.css              # Global styles and CSS variables
```

## Technologies Used

- **React 18** - Modern React with hooks
- **React Router DOM** - Client-side routing
- **Custom CSS** - Tailwind-inspired utility classes
- **SVG Icons** - Inline SVG icons for better performance
- **Responsive Design** - Mobile-first approach

## Customization

### Colors
Edit the CSS variables in `src/index.css`:
```css
:root {
  --primary-color: #3b82f6;
  --secondary-color: #1e40af;
  --accent-color: #f59e0b;
  /* ... more variables */
}
```

### Content
- Update company information in the respective page components
- Modify project data in `src/pages/Projects.js`
- Change contact information in `src/pages/Contact.js`

## Deployment

### Build for Production
```bash
npm run build
```

This creates a `build` folder with optimized production files.

### Deploy Options
- **Netlify** - Drag and drop the `build` folder
- **Vercel** - Connect your GitHub repository
- **GitHub Pages** - Use `gh-pages` package
- **Traditional Hosting** - Upload `build` folder contents

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Contact

- **Email**: tusharsharma.vedanet@gmail.com
- **Website**: www.vedanet-solutions.com


