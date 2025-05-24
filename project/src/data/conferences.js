const baseUrl = window.location.origin

export const conferences = [
  {
    id: 1,
    title: 'International AI Symposium 2025',
    date: 'April 22–24, 2025',
    location: 'Bratislava, Slovakia',
    description: 'Explore the future of AI with global experts.',
    heroImage: `${baseUrl}/conf/photo-1560439514-4e9645039924.jpeg`,
    gallery: [
      `${baseUrl}/conf/conf-test-1.jpg`,
      `${baseUrl}/conf/conf-test-2.jpg`,
      `${baseUrl}/conf/conf-test-3.jpg`
    ],
    speakers: [
      {
        name: 'Anna Nowak',
        photo: 'https://randomuser.me/api/portraits/women/44.jpg',
        role: 'AI Researcher',
        workplace: 'Warsaw University'
      },
      {
        name: 'Samuel Green',
        photo: 'https://randomuser.me/api/portraits/men/32.jpg',
        role: 'Data Scientist',
        workplace: 'MIT'
      },
      {
        name: 'Dr. Emília Čechová',
        photo: 'https://randomuser.me/api/portraits/women/65.jpg',
        role: 'Professor',
        workplace: 'Slovak Technical University'
      }
    ],
    files: [
      { name: 'Conference Program', url: '/docs/program-ai2025.pdf' },
      { name: 'Call for Papers', url: '/docs/cfp-ai2025.docx' }
    ]
  },

  // ...more conferences
]
