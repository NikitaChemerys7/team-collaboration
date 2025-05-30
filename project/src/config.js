export const API_URL = 'http://127.0.0.1:8000/api'

export const UPLOAD_MAX_SIZE = 10 * 1024 * 1024 // 10MB

export const UNIVERSITIES = [
  {
    id: 1,
    name: 'University of Ljubljana',
    shortName: 'UL',
    location: 'Ljubljana, Slovenia',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Univerza_v_Ljubljani_logo.svg/800px-Univerza_v_Ljubljani_logo.svg.png',
    website: 'https://www.uni-lj.si/eng/'
  },
  {
    id: 2,
    name: 'University of Zagreb',
    shortName: 'UNIZG',
    location: 'Zagreb, Croatia',
    logo: 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7d/University_of_Zagreb_Logo.svg/1200px-University_of_Zagreb_Logo.svg.png',
    website: 'http://www.unizg.hr/homepage/'
  },
  {
    id: 3,
    name: 'Josip Juraj Strossmayer University of Osijek',
    shortName: 'UNIOS',
    location: 'Osijek, Croatia',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a1/Sveuciliste_Josipa_Jurja_Strossmayera_u_Osijeku_logo.svg/1200px-Sveuciliste_Josipa_Jurja_Strossmayera_u_Osijeku_logo.svg.png',
    website: 'https://www.unios.hr/en/'
  },
  {
    id: 4,
    name: 'BOKU University',
    shortName: 'BOKU',
    location: 'Vienna, Austria',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Logo_Universit%C3%A4t_f%C3%BCr_Bodenkultur_Wien.svg/1200px-Logo_Universit%C3%A4t_f%C3%BCr_Bodenkultur_Wien.svg.png',
    website: 'https://boku.ac.at/en/'
  },
  {
    id: 5,
    name: 'University of Padua',
    shortName: 'UNIPD',
    location: 'Padua, Italy',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/d/d8/Universit%C3%A0_di_Padova_-_Stemma.png',
    website: 'https://www.unipd.it/en/'
  },
  {
    id: 6,
    name: 'Czech University of Life Sciences Prague',
    shortName: 'CZU',
    location: 'Prague, Czech Republic',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/7/7f/CZU_logo_text_EN.jpg',
    website: 'https://www.czu.cz/en/'
  },
  {
    id: 7,
    name: 'Hungarian University of Agricultural and Life Sciences',
    shortName: 'MATE',
    location: 'Hungary',
    logo: 'https://uni-mate.hu/sites/default/files/mate_main_en.png',
    website: 'https://uni-mate.hu/en'
  },
  {
    id: 8,
    name: 'Slovak University of Agriculture in Nitra',
    shortName: 'SUA',
    location: 'Nitra, Slovakia',
    logo: 'https://www.uniag.sk/en/storage/app/uploads/public/5f3/52f/52c/5f352f52c1c5e975887905.jpg',
    website: 'https://www.uniag.sk/en/'
  }
]

export const VALIDATION = {
  MIN_PASSWORD_LENGTH: 8,
  MAX_TITLE_LENGTH: 100,
  MAX_CONTENT_LENGTH: 100000
}

export const SUPPORTED_FILE_TYPES = [
  '.doc',
  '.docx',
  '.pdf'
]