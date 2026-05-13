// src/types/specialtyForms.ts
export interface SpecialtyQuestion {
    id: string;
    question: string;
    type: 'text' | 'select' | 'multiselect' | 'number' | 'radio';
    options?: string[];
    required: boolean;
    placeholder?: string;
}

export interface SpecialtyFormConfig {
    specialty: string;
    title: string;
    description: string;
    questions: SpecialtyQuestion[];
}

export const specialtyForms: Record<string, SpecialtyFormConfig> = {
    'قلب و عروق': {
        specialty: 'قلب و عروق',
        title: 'فرم تکمیلی قلب و عروق',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر وضعیت قلبی خود پاسخ دهید',
        questions: [
            {
                id: 'chest_pain_duration',
                question: 'درد قفسه سینه چند دقیقه طول می‌کشد؟',
                type: 'select',
                options: ['کمتر از 5 دقیقه', '5 تا 15 دقیقه', '15 تا 30 دقیقه', 'بیشتر از 30 دقیقه'],
                required: true
            },
            {
                id: 'pain_radiation',
                question: 'آیا درد به نواحی دیگر (بازو، فک، پشت) منتقل می‌شود؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'heart_rate',
                question: 'ضربان قلب شما در حالت استراحت چند است؟',
                type: 'number',
                placeholder: 'مثال: 75',
                required: false
            },
            {
                id: 'blood_pressure',
                question: 'آخرین فشار خون شما چقدر بود؟',
                type: 'text',
                placeholder: 'مثال: 120/80',
                required: false
            },
            {
                id: 'family_history',
                question: 'آیا سابقه بیماری قلبی در خانواده دارید؟',
                type: 'radio',
                options: ['بله', 'خیر', 'نمی‌دانم'],
                required: true
            },
            {
                id: 'smoking',
                question: 'آیا سیگار می‌کشید؟',
                type: 'select',
                options: ['خیر', 'بله - کمتر از 10 نخ در روز', 'بله - 10 تا 20 نخ در روز', 'بله - بیشتر از 20 نخ در روز'],
                required: true
            }
        ]
    },
    'ارتوپدی': {
        specialty: 'ارتوپدی',
        title: 'فرم تکمیلی ارتوپدی',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر مشکل استخوان و مفاصل خود پاسخ دهید',
        questions: [
            {
                id: 'pain_location',
                question: 'محل دقیق درد کجاست؟',
                type: 'select',
                options: ['گردن', 'شانه', 'آرنج', 'مچ دست', 'کمر', 'زانو', 'مچ پا', 'سایر'],
                required: true
            },
            {
                id: 'pain_intensity',
                question: 'شدت درد را از 1 تا 10 مشخص کنید',
                type: 'number',
                placeholder: '1 (خفیف) تا 10 (شدید)',
                required: true
            },
            {
                id: 'injury_history',
                question: 'آیا اخیراً ضربه یا آسیب دیده‌اید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'movement_limitation',
                question: 'آیا محدودیت حرکتی دارید؟',
                type: 'radio',
                options: ['بله - شدید', 'بله - متوسط', 'بله - خفیف', 'خیر'],
                required: true
            },
            {
                id: 'swelling',
                question: 'آیا تورم یا قرمزی در ناحیه درد وجود دارد؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'previous_surgery',
                question: 'آیا سابقه جراحی ارتوپدی دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: false
            }
        ]
    },
    'مغز و اعصاب': {
        specialty: 'مغز و اعصاب',
        title: 'فرم تکمیلی مغز و اعصاب',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر وضعیت عصبی خود پاسخ دهید',
        questions: [
            {
                id: 'headache_type',
                question: 'نوع سردرد شما چگونه است؟',
                type: 'select',
                options: ['تپشی', 'فشاری', 'سوزشی', 'یک طرفه', 'دو طرفه'],
                required: true
            },
            {
                id: 'headache_frequency',
                question: 'چند بار در هفته سردرد دارید؟',
                type: 'select',
                options: ['روزانه', '3-5 بار در هفته', '1-2 بار در هفته', 'کمتر از یک بار در هفته'],
                required: true
            },
            {
                id: 'vision_problems',
                question: 'آیا مشکل بینایی یا تاری دید دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'numbness',
                question: 'آیا بی‌حسی یا گزگز در دست یا پا دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'balance_issues',
                question: 'آیا مشکل تعادل یا سرگیجه دارید؟',
                type: 'radio',
                options: ['بله - شدید', 'بله - خفیف', 'خیر'],
                required: true
            },
            {
                id: 'seizure_history',
                question: 'آیا سابقه تشنج دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            }
        ]
    },
    'گوارش': {
        specialty: 'گوارش',
        title: 'فرم تکمیلی گوارش',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر مشکل گوارشی خود پاسخ دهید',
        questions: [
            {
                id: 'pain_location',
                question: 'محل درد شکمی کجاست؟',
                type: 'select',
                options: ['بالای شکم', 'وسط شکم', 'پایین شکم', 'سمت راست', 'سمت چپ', 'کل شکم'],
                required: true
            },
            {
                id: 'pain_timing',
                question: 'درد چه زمانی بیشتر می‌شود؟',
                type: 'select',
                options: ['قبل از غذا', 'بعد از غذا', 'شب‌ها', 'همیشه', 'نامنظم'],
                required: true
            },
            {
                id: 'nausea_vomiting',
                question: 'آیا تهوع یا استفراغ دارید؟',
                type: 'radio',
                options: ['بله - شدید', 'بله - خفیف', 'خیر'],
                required: true
            },
            {
                id: 'bowel_habits',
                question: 'وضعیت دفع شما چگونه است؟',
                type: 'select',
                options: ['طبیعی', 'اسهال', 'یبوست', 'متناوب (اسهال و یبوست)'],
                required: true
            },
            {
                id: 'blood_in_stool',
                question: 'آیا خون در مدفوع مشاهده کرده‌اید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'weight_change',
                question: 'آیا اخیراً کاهش وزن ناخواسته داشته‌اید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            }
        ]
    },
    'ریه و تنفس': {
        specialty: 'ریه و تنفس',
        title: 'فرم تکمیلی ریه و تنفس',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر مشکل تنفسی خود پاسخ دهید',
        questions: [
            {
                id: 'cough_duration',
                question: 'سرفه شما چند وقت است ادامه دارد؟',
                type: 'select',
                options: ['کمتر از یک هفته', '1-2 هفته', '2-4 هفته', 'بیشتر از یک ماه'],
                required: true
            },
            {
                id: 'cough_type',
                question: 'نوع سرفه شما چگونه است؟',
                type: 'select',
                options: ['خشک', 'با خلط', 'با خون', 'شبانه'],
                required: true
            },
            {
                id: 'breathing_difficulty',
                question: 'تنگی نفس در چه شرایطی بیشتر است؟',
                type: 'select',
                options: ['در حال استراحت', 'هنگام فعالیت خفیف', 'هنگام فعالیت شدید', 'شب‌ها', 'همیشه'],
                required: true
            },
            {
                id: 'wheezing',
                question: 'آیا صدای خس‌خس سینه دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'fever',
                question: 'آیا تب دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'smoking_history',
                question: 'سابقه مصرف سیگار',
                type: 'select',
                options: ['هرگز', 'سابقاً (ترک کرده)', 'فعلاً می‌کشم'],
                required: true
            }
        ]
    },
    'غدد و متابولیسم': {
        specialty: 'غدد و متابولیسم',
        title: 'فرم تکمیلی غدد و متابولیسم',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر وضعیت هورمونی خود پاسخ دهید',
        questions: [
            {
                id: 'weight_change',
                question: 'آیا تغییر وزن ناگهانی داشته‌اید؟',
                type: 'select',
                options: ['افزایش وزن', 'کاهش وزن', 'بدون تغییر'],
                required: true
            },
            {
                id: 'fatigue_level',
                question: 'سطح خستگی شما چگونه است؟',
                type: 'select',
                options: ['خفیف', 'متوسط', 'شدید', 'بدون خستگی'],
                required: true
            },
            {
                id: 'thirst_urination',
                question: 'آیا تشنگی یا ادرار زیاد دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'heat_cold_intolerance',
                question: 'آیا حساسیت به گرما یا سرما دارید؟',
                type: 'select',
                options: ['حساسیت به گرما', 'حساسیت به سرما', 'هر دو', 'هیچکدام'],
                required: true
            },
            {
                id: 'hair_skin_changes',
                question: 'آیا تغییرات پوستی یا ریزش مو دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: false
            },
            {
                id: 'diabetes_history',
                question: 'آیا سابقه دیابت در خانواده دارید؟',
                type: 'radio',
                options: ['بله', 'خیر', 'نمی‌دانم'],
                required: true
            }
        ]
    },
    'کلیه و مجاری ادراری': {
        specialty: 'کلیه و مجاری ادراری',
        title: 'فرم تکمیلی کلیه و مجاری ادراری',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر مشکل کلیوی خود پاسخ دهید',
        questions: [
            {
                id: 'urination_frequency',
                question: 'تعداد دفعات ادرار در شبانه‌روز',
                type: 'select',
                options: ['طبیعی (4-7 بار)', 'زیاد (بیش از 8 بار)', 'کم (کمتر از 4 بار)'],
                required: true
            },
            {
                id: 'pain_location',
                question: 'محل درد کجاست؟',
                type: 'select',
                options: ['کمر (پهلو)', 'پایین شکم', 'هنگام ادرار', 'بدون درد'],
                required: true
            },
            {
                id: 'urine_color',
                question: 'رنگ ادرار چگونه است؟',
                type: 'select',
                options: ['طبیعی (زرد روشن)', 'تیره', 'قرمز یا خونی', 'کدر'],
                required: true
            },
            {
                id: 'burning_sensation',
                question: 'آیا سوزش هنگام ادرار دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'swelling',
                question: 'آیا تورم در پاها یا صورت دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'kidney_stone_history',
                question: 'آیا سابقه سنگ کلیه دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: false
            }
        ]
    },
    'پوست': {
        specialty: 'پوست',
        title: 'فرم تکمیلی پوست',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر مشکل پوستی خود پاسخ دهید',
        questions: [
            {
                id: 'rash_location',
                question: 'محل ضایعه پوستی کجاست؟',
                type: 'text',
                placeholder: 'مثال: صورت، دست، پا',
                required: true
            },
            {
                id: 'rash_duration',
                question: 'ضایعه پوستی چند وقت است وجود دارد؟',
                type: 'select',
                options: ['کمتر از یک هفته', '1-2 هفته', '2-4 هفته', 'بیشتر از یک ماه'],
                required: true
            },
            {
                id: 'itching',
                question: 'آیا خارش دارید؟',
                type: 'radio',
                options: ['بله - شدید', 'بله - خفیف', 'خیر'],
                required: true
            },
            {
                id: 'rash_appearance',
                question: 'ظاهر ضایعه چگونه است؟',
                type: 'select',
                options: ['قرمز', 'تاول', 'پوسته‌پوسته', 'زخم', 'تورم', 'سایر'],
                required: true
            },
            {
                id: 'pain',
                question: 'آیا درد دارد؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'allergy_history',
                question: 'آیا سابقه آلرژی پوستی دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: false
            }
        ]
    },
    'روانپزشکی': {
        specialty: 'روانپزشکی',
        title: 'فرم تکمیلی روانپزشکی',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر وضعیت روانی خود پاسخ دهید',
        questions: [
            {
                id: 'mood',
                question: 'وضعیت خلقی شما در دو هفته اخیر چگونه بوده؟',
                type: 'select',
                options: ['افسرده', 'مضطرب', 'عصبی', 'بی‌حال', 'طبیعی'],
                required: true
            },
            {
                id: 'sleep_quality',
                question: 'کیفیت خواب شما چگونه است؟',
                type: 'select',
                options: ['خوب', 'بی‌خوابی', 'خواب زیاد', 'خواب متناوب'],
                required: true
            },
            {
                id: 'appetite',
                question: 'اشتهای غذایی شما چگونه است؟',
                type: 'select',
                options: ['طبیعی', 'کاهش یافته', 'افزایش یافته', 'بدون اشتها'],
                required: true
            },
            {
                id: 'concentration',
                question: 'آیا مشکل تمرکز دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'anxiety_attacks',
                question: 'آیا حملات اضطراب یا پانیک دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'previous_treatment',
                question: 'آیا سابقه درمان روانپزشکی دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: false
            }
        ]
    },
    'چشم': {
        specialty: 'چشم',
        title: 'فرم تکمیلی چشم',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر مشکل چشمی خود پاسخ دهید',
        questions: [
            {
                id: 'vision_problem',
                question: 'نوع مشکل بینایی شما چیست؟',
                type: 'select',
                options: ['تاری دید', 'دوبینی', 'نقاط سیاه', 'درد چشم', 'قرمزی', 'سایر'],
                required: true
            },
            {
                id: 'problem_duration',
                question: 'مشکل چند وقت است شروع شده؟',
                type: 'select',
                options: ['امروز', 'چند روز', 'یک هفته', 'بیشتر از یک هفته'],
                required: true
            },
            {
                id: 'eye_affected',
                question: 'کدام چشم درگیر است؟',
                type: 'radio',
                options: ['راست', 'چپ', 'هر دو'],
                required: true
            },
            {
                id: 'light_sensitivity',
                question: 'آیا حساسیت به نور دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'discharge',
                question: 'آیا ترشح از چشم دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'glasses',
                question: 'آیا از عینک یا لنز استفاده می‌کنید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: false
            }
        ]
    },
    'گوش و حلق و بینی': {
        specialty: 'گوش و حلق و بینی',
        title: 'فرم تکمیلی گوش و حلق و بینی',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر مشکل خود پاسخ دهید',
        questions: [
            {
                id: 'problem_location',
                question: 'محل اصلی مشکل کجاست؟',
                type: 'select',
                options: ['گوش', 'حلق', 'بینی', 'چند ناحیه'],
                required: true
            },
            {
                id: 'pain_severity',
                question: 'شدت درد از 1 تا 10',
                type: 'number',
                placeholder: '1 (خفیف) تا 10 (شدید)',
                required: true
            },
            {
                id: 'hearing_problem',
                question: 'آیا مشکل شنوایی دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'nasal_congestion',
                question: 'آیا گرفتگی بینی دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'sore_throat',
                question: 'آیا گلودرد دارید؟',
                type: 'radio',
                options: ['بله - شدید', 'بله - خفیف', 'خیر'],
                required: true
            },
            {
                id: 'fever',
                question: 'آیا تب دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            }
        ]
    },
    'زنان و زایمان': {
        specialty: 'زنان و زایمان',
        title: 'فرم تکمیلی زنان و زایمان',
        description: 'لطفاً اطلاعات تکمیلی زیر را برای بررسی دقیق‌تر مشکل خود پاسخ دهید',
        questions: [
            {
                id: 'menstrual_cycle',
                question: 'وضعیت قاعدگی شما چگونه است؟',
                type: 'select',
                options: ['منظم', 'نامنظم', 'قطع شده', 'خونریزی زیاد', 'دردناک'],
                required: true
            },
            {
                id: 'pain_location',
                question: 'محل درد کجاست؟',
                type: 'select',
                options: ['پایین شکم', 'لگن', 'کمر', 'چند ناحیه'],
                required: true
            },
            {
                id: 'pregnancy_status',
                question: 'آیا احتمال بارداری وجود دارد؟',
                type: 'radio',
                options: ['بله', 'خیر', 'نمی‌دانم'],
                required: true
            },
            {
                id: 'discharge',
                question: 'آیا ترشح غیرطبیعی دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: true
            },
            {
                id: 'pain_timing',
                question: 'درد چه زمانی بیشتر است؟',
                type: 'select',
                options: ['همیشه', 'در زمان قاعدگی', 'بین دو قاعدگی', 'نامنظم'],
                required: true
            },
            {
                id: 'previous_pregnancy',
                question: 'آیا سابقه بارداری دارید؟',
                type: 'radio',
                options: ['بله', 'خیر'],
                required: false
            }
        ]
    }
};
