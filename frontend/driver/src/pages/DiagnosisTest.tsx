// DiagnosisTest.tsx
import React, { useState } from 'react';
import {
    Box,
    Container,
    TextField,
    Button,
    Paper,
    Typography,
    CircularProgress,
    Alert,
    FormControl,
    InputLabel,
    Select,
    MenuItem,
    Chip,
    Stack,
    useMediaQuery,
    Divider,
    Card,
    CardContent,
    alpha,
} from '@mui/material';
import {
    Send as SendIcon,
    LocalHospital as HospitalIcon,
    Speed as SpeedIcon,
    Warning as WarningIcon,
    Science as ScienceIcon,
    FitnessCenter as FitnessIcon,
    Lightbulb as LightbulbIcon,
    Notes as NotesIcon,
} from '@mui/icons-material';
import { useTheme } from "@mui/material/styles";
import { specialtyForms } from '../types/specialtyForms';
import { specialtyDoctorsAndLabs, SpecialtyKey } from '../types/doctorsAndLabs';

import SpecialtyForm from '../components/SpecialtyForm';

interface DiagnosisRequest {
    symptoms: string;
    age?: number;
    gender?: 'male' | 'female';
    medical_history?: string;
}

interface SpecialtyInfo {
    primary: string;
    secondary?: string[];
    recommended_specialist: string;
}

interface DiagnosisResponse {
    specialty: SpecialtyInfo;
    urgency_level: string;
    diagnosis: string[];
    diagnosis_description?: string;
    red_flags: string[];
    recommended_tests: string[];
    recommended_exercises: string[];
    lifestyle_changes: string[];
    notes: string;
}

const DiagnosisTest: React.FC = () => {
    const [formData, setFormData] = useState<DiagnosisRequest>({
        symptoms: '',
        age: undefined,
        gender: undefined,
        medical_history: '',
    });

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [result, setResult] = useState<DiagnosisResponse | null>(null);
    const [showSpecialtyForm, setShowSpecialtyForm] = useState(false);
    const [showDetails, setShowDetails] = useState(false);

    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("sm"));

    const getUrgencyColor = (level: string): "error" | "warning" | "success" | "default" => {
        switch (level) {
            case 'اورژانسی':
                return 'error';
            case 'نیاز به ویزیت سریع':
                return 'warning';
            case 'غیرفوری':
                return 'success';
            default:
                return 'default';
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError(null);
        setResult(null);
        setShowSpecialtyForm(false);
        setShowDetails(false);

        try {
            const response = await fetch('http://185.222.163.113:8000/diagnose', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
            });

            if (!response.ok) throw new Error('خطا در دریافت پاسخ از سرور');

            const data: DiagnosisResponse = await response.json();
            setResult(data);
        } catch (err) {
            setError('خطا در ارتباط با سرور');
        } finally {
            setLoading(false);
        }
    };

    const handleSpecialtyFormSubmit = (answers: Record<string, string>) => {
        console.log('Specialty form answers:', answers);
        setShowSpecialtyForm(false);
        setShowDetails(true);
    };

    const handleSpecialtyFormSkip = () => {
        setShowSpecialtyForm(false);
        setShowDetails(true);
    };

    return (
        <Box
            sx={{
                minHeight: '100vh',
                background: `linear-gradient(135deg, ${alpha(theme.palette.primary.light, 0.1)} 0%, ${alpha(theme.palette.secondary.light, 0.05)} 100%)`,
                py: { xs: 2, sm: 4, md: 6 },
            }}
        >
            <Container maxWidth="lg">
                <Paper
                    elevation={0}
                    sx={{
                        p: { xs: 2, sm: 3, md: 4 },
                        borderRadius: 3,
                        boxShadow: '0 8px 32px rgba(0,0,0,0.08)',
                    }}
                >
                    <Typography
                        variant={isMobile ? 'h5' : 'h4'}
                        fontWeight="700"
                        color="primary"
                        gutterBottom
                        sx={{ mb: 3 }}
                    >
                        سیستم تشخیص پزشکی
                    </Typography>

                    <Box component="form" onSubmit={handleSubmit}>
                        <Stack spacing={2}>
                            <TextField
                                fullWidth
                                multiline
                                rows={4}
                                label="علائم خود را شرح دهید"
                                value={formData.symptoms}
                                onChange={(e) =>
                                    setFormData({ ...formData, symptoms: e.target.value })
                                }
                                required
                                variant="outlined"
                                sx={{
                                    '& .MuiOutlinedInput-root': {
                                        borderRadius: 2,
                                    },
                                }}
                            />

                            <Stack direction={{ xs: 'column', sm: 'row' }} spacing={2}>
                                <TextField
                                    fullWidth
                                    type="number"
                                    label="سن"
                                    value={formData.age || ''}
                                    onChange={(e) =>
                                        setFormData({ ...formData, age: Number(e.target.value) })
                                    }
                                    sx={{
                                        '& .MuiOutlinedInput-root': {
                                            borderRadius: 2,
                                        },
                                    }}
                                />

                                <FormControl fullWidth>
                                    <InputLabel>جنسیت</InputLabel>
                                    <Select
                                        value={formData.gender || ''}
                                        label="جنسیت"
                                        onChange={(e) =>
                                            setFormData({
                                                ...formData,
                                                gender: e.target.value as 'male' | 'female',
                                            })
                                        }
                                        sx={{ borderRadius: 2 }}
                                    >
                                        <MenuItem value="male">مرد</MenuItem>
                                        <MenuItem value="female">زن</MenuItem>
                                    </Select>
                                </FormControl>
                            </Stack>

                            <TextField
                                fullWidth
                                multiline
                                rows={2}
                                label="سابقه پزشکی (اختیاری)"
                                value={formData.medical_history}
                                onChange={(e) =>
                                    setFormData({
                                        ...formData,
                                        medical_history: e.target.value,
                                    })
                                }
                                sx={{
                                    '& .MuiOutlinedInput-root': {
                                        borderRadius: 2,
                                    },
                                }}
                            />

                            <Button
                                type="submit"
                                variant="contained"
                                size="large"
                                startIcon={loading ? null : <SendIcon />}
                                disabled={loading}
                                fullWidth
                                sx={{
                                    py: 1.5,
                                    borderRadius: 2,
                                    fontWeight: 'bold',
                                    fontSize: '1rem',
                                }}
                            >
                                {loading ? <CircularProgress size={24} color="inherit" /> : 'دریافت تشخیص'}
                            </Button>
                        </Stack>
                    </Box>

                    {error && (
                        <Alert severity="error" sx={{ mt: 3, borderRadius: 2 }}>
                            {error}
                        </Alert>
                    )}

                    {result && !showSpecialtyForm && !showDetails && (
                        <Card
                            sx={{
                                mt: 4,
                                borderRadius: 3,
                                boxShadow: '0 4px 20px rgba(0,0,0,0.08)',
                                background: alpha(theme.palette.info.light, 0.05),
                            }}
                        >
                            <CardContent sx={{ p: { xs: 2, sm: 3 } }}>
                                <Typography
                                    variant="h6"
                                    fontWeight="600"
                                    color="primary"
                                    gutterBottom
                                    sx={{ mb: 2 }}
                                >
                                    توضیحات بیماری
                                </Typography>
                                <Typography
                                    variant="body1"
                                    sx={{
                                        mb: 3,
                                        lineHeight: 1.8,
                                        color: 'text.secondary',
                                    }}
                                >
                                    {result.diagnosis_description}
                                </Typography>

                                {result.specialty.primary && specialtyDoctorsAndLabs[result.specialty.primary as SpecialtyKey] && (
                                    <Box sx={{ mt: 3, display: 'flex', gap: 2, flexDirection: { xs: 'column', md: 'row' } }}>
                                        {/* دکتر پیشنهادی - سمت راست */}
                                        <Box sx={{
                                            flex: 1,
                                            bgcolor: 'white',
                                            border: '1px solid #e5e7eb',
                                            borderRadius: 3,
                                            p: { xs: 2, md: 3 },
                                            boxShadow: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                                            '&:hover': { boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' },
                                            transition: 'box-shadow 0.2s'
                                        }}>
                                            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: { xs: 2, md: 3 }, justifyContent: 'center' }}>
                                                <span style={{ fontSize: '20px' }}>👨‍⚕️</span>
                                                <Typography variant="subtitle2" fontWeight={600} color="text.primary" sx={{ fontSize: { xs: '0.875rem', md: '1rem' } }}>
                                                    دکتر پیشنهادی
                                                </Typography>
                                            </Box>
                                            <Box sx={{
                                                display: 'flex',
                                                gap: { xs: 1.5, md: 2 },
                                                flexWrap: 'wrap',
                                                justifyContent: 'center'
                                            }}>
                                                {specialtyDoctorsAndLabs[result.specialty.primary as SpecialtyKey].doctors.map(doctor => (
                                                    <Box key={doctor.id} sx={{
                                                        display: 'flex',
                                                        flexDirection: 'column',
                                                        alignItems: 'center',
                                                        minWidth: { xs: '90px', sm: '100px', md: '120px' },
                                                        maxWidth: { xs: '90px', sm: '100px', md: '120px' }
                                                    }}>
                                                        <Box sx={{ position: 'relative' }}>
                                                            <Box
                                                                component="img"
                                                                src="https://www.tarhdokan.com/wp-content/uploads/2020/07/doctor-1-1.jpg"
                                                                alt={doctor.name}
                                                                sx={{
                                                                    width: { xs: 60, sm: 70, md: 80 },
                                                                    height: { xs: 60, sm: 70, md: 80 },
                                                                    borderRadius: '50%',
                                                                    objectFit: 'cover',
                                                                    border: '2px solid #e5e7eb'
                                                                }}
                                                            />
                                                            <Box sx={{
                                                                position: 'absolute',
                                                                bottom: { xs: -6, md: -8 },
                                                                right: { xs: -6, md: -8 },
                                                                bgcolor: 'white',
                                                                borderRadius: 2,
                                                                px: { xs: 0.5, md: 1 },
                                                                py: 0.25,
                                                                display: 'flex',
                                                                alignItems: 'center',
                                                                gap: 0.3,
                                                                boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
                                                                border: '1px solid #e5e7eb'
                                                            }}>
                                                                <span style={{ color: '#eab308', fontSize: '12px' }}>⭐</span>
                                                                <Typography variant="caption" fontWeight={600} color="text.primary" sx={{ fontSize: { xs: '0.65rem', md: '0.75rem' } }}>
                                                                    {doctor.rating}
                                                                </Typography>
                                                            </Box>
                                                        </Box>
                                                        <Typography
                                                            variant="body2"
                                                            fontWeight={600}
                                                            color="text.primary"
                                                            sx={{
                                                                mt: { xs: 1.5, md: 2 },
                                                                textAlign: 'center',
                                                                fontSize: { xs: '0.7rem', sm: '0.75rem', md: '0.875rem' },
                                                                lineHeight: 1.3,
                                                                wordBreak: 'break-word'
                                                            }}
                                                        >
                                                            {doctor.name}
                                                        </Typography>
                                                    </Box>
                                                ))}
                                            </Box>
                                        </Box>

                                        {/* آزمایشگاه پیشنهادی - سمت چپ */}
                                        <Box sx={{
                                            flex: 1,
                                            bgcolor: 'white',
                                            border: '1px solid #e5e7eb',
                                            borderRadius: 3,
                                            p: { xs: 2, md: 3 },
                                            boxShadow: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                                            '&:hover': { boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' },
                                            transition: 'box-shadow 0.2s'
                                        }}>
                                            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: { xs: 2, md: 3 }, justifyContent: 'center' }}>
                                                <span style={{ fontSize: '20px' }}>🔬</span>
                                                <Typography variant="subtitle2" fontWeight={600} color="text.primary" sx={{ fontSize: { xs: '0.875rem', md: '1rem' } }}>
                                                    آزمایشگاه پیشنهادی
                                                </Typography>
                                            </Box>
                                            <Box sx={{
                                                display: 'flex',
                                                gap: { xs: 1.5, md: 2 },
                                                flexWrap: 'wrap',
                                                justifyContent: 'center'
                                            }}>
                                                {specialtyDoctorsAndLabs[result.specialty.primary as SpecialtyKey].labs.map(lab => (
                                                    <Box key={lab.id} sx={{
                                                        display: 'flex',
                                                        flexDirection: 'column',
                                                        alignItems: 'center',
                                                        minWidth: { xs: '90px', sm: '100px', md: '120px' },
                                                        maxWidth: { xs: '90px', sm: '100px', md: '120px' }
                                                    }}>
                                                        <Box sx={{ position: 'relative' }}>
                                                            <Box
                                                                component="img"
                                                                src="https://photobag.ir/mihantarh/img/product/9545905_big.jpg?q=85"
                                                                alt={lab.name}
                                                                sx={{
                                                                    width: { xs: 60, sm: 70, md: 80 },
                                                                    height: { xs: 60, sm: 70, md: 80 },
                                                                    borderRadius: '50%',
                                                                    objectFit: 'cover',
                                                                    border: '2px solid #e5e7eb'
                                                                }}
                                                            />
                                                            <Box sx={{
                                                                position: 'absolute',
                                                                bottom: { xs: -6, md: -8 },
                                                                right: { xs: -6, md: -8 },
                                                                bgcolor: 'white',
                                                                borderRadius: 2,
                                                                px: { xs: 0.5, md: 1 },
                                                                py: 0.25,
                                                                display: 'flex',
                                                                alignItems: 'center',
                                                                gap: 0.3,
                                                                boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
                                                                border: '1px solid #e5e7eb'
                                                            }}>
                                                                <span style={{ color: '#eab308', fontSize: '12px' }}>⭐</span>
                                                                <Typography variant="caption" fontWeight={600} color="text.primary" sx={{ fontSize: { xs: '0.65rem', md: '0.75rem' } }}>
                                                                    {lab.rating}
                                                                </Typography>
                                                            </Box>
                                                        </Box>
                                                        <Typography
                                                            variant="body2"
                                                            fontWeight={600}
                                                            color="text.primary"
                                                            sx={{
                                                                mt: { xs: 1.5, md: 2 },
                                                                textAlign: 'center',
                                                                fontSize: { xs: '0.7rem', sm: '0.75rem', md: '0.875rem' },
                                                                lineHeight: 1.3,
                                                                wordBreak: 'break-word'
                                                            }}
                                                        >
                                                            {lab.name}
                                                        </Typography>
                                                    </Box>
                                                ))}
                                            </Box>
                                        </Box>
                                    </Box>
                                )}




                                <Button
                                    variant="contained"
                                    size="large"
                                    startIcon={<HospitalIcon />}
                                    onClick={() => setShowSpecialtyForm(true)}
                                    fullWidth={isMobile}
                                    sx={{
                                        mt:2,
                                        py: 1.5,
                                        borderRadius: 2,
                                        fontWeight: 'bold',
                                    }}
                                >
                                    ارتباط با  {result.specialty.recommended_specialist} و تکمیل اطلاعات
                                </Button>
                            </CardContent>
                        </Card>
                    )}

                    {result && showSpecialtyForm && !showDetails && specialtyForms[result.specialty.primary] && (
                        <Box sx={{ mt: 4 }}>
                            <SpecialtyForm
                                config={specialtyForms[result.specialty.primary]}
                                onSubmit={handleSpecialtyFormSubmit}
                                onSkip={handleSpecialtyFormSkip}
                            />
                        </Box>
                    )}

                    {result && showDetails && (
                        <Box sx={{ mt: 4 }}>
                            <Divider sx={{ mb: 4 }} />

                            <Stack spacing={3}>
                                {/* تشخیص احتمالی */}
                                <Card
                                    sx={{
                                        borderRadius: 2,
                                        boxShadow: '0 2px 12px rgba(0,0,0,0.06)',
                                    }}
                                >
                                    <CardContent>
                                        <Typography variant="h6" fontWeight="600" gutterBottom>
                                            تشخیص احتمالی
                                        </Typography>
                                        <Stack direction="row" spacing={1} flexWrap="wrap" sx={{ gap: 1 }}>
                                            {result.diagnosis.map((d, i) => (
                                                <Chip
                                                    key={i}
                                                    label={d}
                                                    color="error"
                                                    variant="outlined"
                                                    sx={{ fontWeight: 500 }}
                                                />
                                            ))}
                                        </Stack>
                                    </CardContent>
                                </Card>

                                {/* سطح فوریت و علائم خطر */}
                                <Stack direction={{ xs: 'column', sm: 'row' }} spacing={3}>
                                    <Card
                                        sx={{
                                            flex: 1,
                                            borderRadius: 2,
                                            boxShadow: '0 2px 12px rgba(0,0,0,0.06)',
                                        }}
                                    >
                                        <CardContent>
                                            <Typography variant="h6" fontWeight="600" gutterBottom>
                                                سطح فوریت
                                            </Typography>
                                            <Chip
                                                icon={<SpeedIcon />}
                                                label={result.urgency_level}
                                                color={getUrgencyColor(result.urgency_level)}
                                                sx={{ fontWeight: 600, fontSize: '0.95rem' }}
                                            />
                                        </CardContent>
                                    </Card>

                                    {result.red_flags && result.red_flags.length > 0 && (
                                        <Card
                                            sx={{
                                                flex: 1,
                                                borderRadius: 2,
                                                boxShadow: '0 2px 12px rgba(0,0,0,0.06)',
                                                borderLeft: `4px solid ${theme.palette.error.main}`,
                                            }}
                                        >
                                            <CardContent>
                                                <Stack direction="row" spacing={1} alignItems="center" sx={{ mb: 1.5 }}>
                                                    <WarningIcon color="error" />
                                                    <Typography variant="h6" fontWeight="600" color="error">
                                                        علائم خطر
                                                    </Typography>
                                                </Stack>
                                                {result.red_flags.map((flag, i) => (
                                                    <Typography
                                                        key={i}
                                                        color="error"
                                                        sx={{ mb: 0.5, fontSize: '0.9rem' }}
                                                    >
                                                        • {flag}
                                                    </Typography>
                                                ))}
                                            </CardContent>
                                        </Card>
                                    )}
                                </Stack>

                                {/* آزمایش‌ها و ورزش‌ها */}
                                <Stack direction={{ xs: 'column', md: 'row' }} spacing={3}>
                                    <Card
                                        sx={{
                                            flex: 1,
                                            borderRadius: 2,
                                            boxShadow: '0 2px 12px rgba(0,0,0,0.06)',
                                        }}
                                    >
                                        <CardContent>
                                            <Stack direction="row" spacing={1} alignItems="center" sx={{ mb: 1.5 }}>
                                                <ScienceIcon color="primary" />
                                                <Typography variant="h6" fontWeight="600">
                                                    آزمایش‌های پیشنهادی
                                                </Typography>
                                            </Stack>
                                            {result.recommended_tests.map((t, i) => (
                                                <Typography key={i} sx={{ mb: 0.5, fontSize: '0.9rem' }}>
                                                    • {t}
                                                </Typography>
                                            ))}
                                        </CardContent>
                                    </Card>

                                    <Card
                                        sx={{
                                            flex: 1,
                                            borderRadius: 2,
                                            boxShadow: '0 2px 12px rgba(0,0,0,0.06)',
                                        }}
                                    >
                                        <CardContent>
                                            <Stack direction="row" spacing={1} alignItems="center" sx={{ mb: 1.5 }}>
                                                <FitnessIcon color="success" />
                                                <Typography variant="h6" fontWeight="600">
                                                    ورزش‌های توصیه شده
                                                </Typography>
                                            </Stack>
                                            {result.recommended_exercises.map((e, i) => (
                                                <Typography key={i} sx={{ mb: 0.5, fontSize: '0.9rem' }}>
                                                    • {e}
                                                </Typography>
                                            ))}
                                        </CardContent>
                                    </Card>
                                </Stack>

                                {/* تغییرات سبک زندگی */}
                                <Card
                                    sx={{
                                        borderRadius: 2,
                                        boxShadow: '0 2px 12px rgba(0,0,0,0.06)',
                                    }}
                                >
                                    <CardContent>
                                        <Stack direction="row" spacing={1} alignItems="center" sx={{ mb: 1.5 }}>
                                            <LightbulbIcon color="warning" />
                                            <Typography variant="h6" fontWeight="600">
                                                تغییرات سبک زندگی
                                            </Typography>
                                        </Stack>
                                        {result.lifestyle_changes.map((l, i) => (
                                            <Typography key={i} sx={{ mb: 0.5, fontSize: '0.9rem' }}>
                                                • {l}
                                            </Typography>
                                        ))}
                                    </CardContent>
                                </Card>

                                {/* یادداشت */}
                                <Card
                                    sx={{
                                        borderRadius: 2,
                                        boxShadow: '0 2px 12px rgba(0,0,0,0.06)',
                                        background: alpha(theme.palette.info.light, 0.05),
                                    }}
                                >
                                    <CardContent>
                                        <Stack direction="row" spacing={1} alignItems="center" sx={{ mb: 1.5 }}>
                                            <NotesIcon color="info" />
                                            <Typography variant="h6" fontWeight="600">
                                                یادداشت
                                            </Typography>
                                        </Stack>
                                        <Typography sx={{ lineHeight: 1.8, fontSize: '0.9rem' }}>
                                            {result.notes}
                                        </Typography>
                                    </CardContent>
                                </Card>
                            </Stack>
                        </Box>
                    )}
                </Paper>
            </Container>
        </Box>
    );
};

export default DiagnosisTest;
