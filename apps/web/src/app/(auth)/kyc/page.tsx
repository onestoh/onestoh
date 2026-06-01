'use client'
import { useState, useCallback } from 'react'
import { useRouter } from 'next/navigation'
import { useDropzone } from 'react-dropzone'
import { useAuthStore } from '@/lib/store'
import api from '@/lib/api'
import toast from 'react-hot-toast'
import { Upload, CheckCircle2, ChevronRight, FileText, Camera, User } from 'lucide-react'

type Step = 'id_type' | 'id_front' | 'id_back' | 'selfie' | 'review'

const STEPS: { key: Step; label: string; icon: React.ReactNode }[] = [
  { key: 'id_type', label: 'ID Type', icon: <FileText className="w-4 h-4" /> },
  { key: 'id_front', label: 'ID Front', icon: <Camera className="w-4 h-4" /> },
  { key: 'id_back', label: 'ID Back', icon: <Camera className="w-4 h-4" /> },
  { key: 'selfie', label: 'Selfie', icon: <User className="w-4 h-4" /> },
  { key: 'review', label: 'Submit', icon: <CheckCircle2 className="w-4 h-4" /> },
]

function DropZone({ label, onFile, preview }: { label: string; onFile: (f: File) => void; preview: string | null }) {
  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    accept: { 'image/*': [] },
    maxFiles: 1,
    onDrop: (files) => files[0] && onFile(files[0]),
  })

  return (
    <div
      {...getRootProps()}
      className={`relative border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors ${
        isDragActive ? 'border-[#E8922A] bg-[#E8922A]/5' : 'border-white/10 hover:border-[#E8922A]/40'
      }`}
    >
      <input {...getInputProps()} />
      {preview ? (
        // eslint-disable-next-line @next/next/no-img-element
        <img src={preview} alt="Preview" className="max-h-48 mx-auto rounded-lg object-contain" />
      ) : (
        <>
          <Upload className="w-8 h-8 text-[#E8922A] mx-auto mb-3" />
          <p className="text-sm text-[#DCE5F2] font-medium">{label}</p>
          <p className="text-xs text-[#7088A8] mt-1">Drag & drop or click to upload. JPG, PNG up to 10MB.</p>
        </>
      )}
    </div>
  )
}

export default function KycPage() {
  const router = useRouter()
  const { user } = useAuthStore()
  const [step, setStep] = useState<Step>('id_type')
  const [idType, setIdType] = useState<'national_id' | 'passport' | 'driving_licence'>('national_id')
  const [idFront, setIdFront] = useState<File | null>(null)
  const [idBack, setIdBack] = useState<File | null>(null)
  const [selfie, setSelfie] = useState<File | null>(null)
  const [previews, setPreviews] = useState<Record<string, string>>({})
  const [loading, setLoading] = useState(false)

  const setFile = (key: string, file: File) => {
    const url = URL.createObjectURL(file)
    setPreviews((p) => ({ ...p, [key]: url }))
    if (key === 'front') setIdFront(file)
    if (key === 'back') setIdBack(file)
    if (key === 'selfie') setSelfie(file)
  }

  const currentStepIdx = STEPS.findIndex((s) => s.key === step)

  const handleSubmit = async () => {
    if (!idFront || !selfie) { toast.error('Please upload all required documents'); return }
    setLoading(true)
    const form = new FormData()
    form.append('id_type', idType)
    form.append('id_front', idFront)
    if (idBack) form.append('id_back', idBack)
    form.append('selfie', selfie)
    try {
      await api.post('/api/kyc/submit', form, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      toast.success('KYC submitted! Under review (usually 24h).')
      if (user?.role === 'client') router.push('/client')
      else if (user?.role === 'yard_owner' || user?.role === 'individual_owner') router.push('/owner')
      else router.push('/client')
    } catch (err: unknown) {
      const msg =
        err && typeof err === 'object' && 'response' in err
          ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
          : undefined
      toast.error(msg || 'Submission failed')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div>
      <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2] mb-1">Identity Verification</h1>
      <p className="text-sm text-[#7088A8] mb-6">Complete KYC to unlock booking and listing features</p>

      {/* Progress */}
      <div className="flex items-center gap-1 mb-8">
        {STEPS.map((s, i) => (
          <div key={s.key} className="flex items-center gap-1 flex-1">
            <div
              className={`w-7 h-7 rounded-full flex items-center justify-center text-xs transition-colors ${
                i < currentStepIdx
                  ? 'bg-[#2ECC8A] text-[#080C12]'
                  : i === currentStepIdx
                  ? 'bg-[#E8922A] text-[#080C12]'
                  : 'bg-[#141D2B] text-[#7088A8]'
              }`}
            >
              {i < currentStepIdx ? <CheckCircle2 className="w-3.5 h-3.5" /> : i + 1}
            </div>
            {i < STEPS.length - 1 && (
              <div className={`flex-1 h-0.5 ${
                i < currentStepIdx ? 'bg-[#2ECC8A]' : 'bg-white/10'
              }`} />
            )}
          </div>
        ))}
      </div>

      <div className="space-y-5">
        {step === 'id_type' && (
          <>
            <p className="text-sm text-[#7088A8]">Select your government-issued ID type</p>
            <div className="space-y-2">
              {([
                { value: 'national_id', label: 'National ID Card', desc: 'Kenya National Identity Card' },
                { value: 'passport', label: 'Passport', desc: 'International travel document' },
                { value: 'driving_licence', label: "Driver's Licence", desc: 'NTSA issued licence' },
              ] as const).map((opt) => (
                <button
                  key={opt.value}
                  onClick={() => setIdType(opt.value)}
                  className={`w-full flex items-center gap-3 p-4 rounded-xl border text-left transition-all ${
                    idType === opt.value ? 'border-[#E8922A] bg-[#E8922A]/5' : 'border-white/5 bg-[#141D2B] hover:border-white/20'
                  }`}
                >
                  <FileText className={`w-5 h-5 ${idType === opt.value ? 'text-[#E8922A]' : 'text-[#7088A8]'}`} />
                  <div>
                    <p className="text-sm font-medium text-[#DCE5F2]">{opt.label}</p>
                    <p className="text-xs text-[#7088A8]">{opt.desc}</p>
                  </div>
                  {idType === opt.value && <CheckCircle2 className="w-4 h-4 text-[#E8922A] ml-auto" />}
                </button>
              ))}
            </div>
            <button onClick={() => setStep('id_front')} className="w-full bg-[#E8922A] text-[#080C12] font-semibold py-3 rounded-lg hover:bg-[#F5B050] transition-colors flex items-center justify-center gap-2">
              Continue <ChevronRight className="w-4 h-4" />
            </button>
          </>
        )}

        {step === 'id_front' && (
          <>
            <p className="text-sm text-[#7088A8]">Upload the <strong className="text-[#DCE5F2]">front side</strong> of your {idType.replace(/_/g, ' ')}</p>
            <DropZone label="Tap or drag ID front image" onFile={(f) => setFile('front', f)} preview={previews.front ?? null} />
            <div className="flex gap-3">
              <button onClick={() => setStep('id_type')} className="flex-1 border border-white/10 text-[#7088A8] py-2.5 rounded-lg hover:border-white/20">Back</button>
              <button onClick={() => idFront && setStep('id_back')} disabled={!idFront} className="flex-1 bg-[#E8922A] text-[#080C12] font-semibold py-2.5 rounded-lg hover:bg-[#F5B050] transition-colors disabled:opacity-50">Continue</button>
            </div>
          </>
        )}

        {step === 'id_back' && (
          <>
            <p className="text-sm text-[#7088A8]">Upload the <strong className="text-[#DCE5F2]">back side</strong> (optional for passport)</p>
            <DropZone label="Tap or drag ID back image" onFile={(f) => setFile('back', f)} preview={previews.back ?? null} />
            <div className="flex gap-3">
              <button onClick={() => setStep('id_front')} className="flex-1 border border-white/10 text-[#7088A8] py-2.5 rounded-lg hover:border-white/20">Back</button>
              <button onClick={() => setStep('selfie')} className="flex-1 bg-[#E8922A] text-[#080C12] font-semibold py-2.5 rounded-lg hover:bg-[#F5B050] transition-colors">Continue</button>
            </div>
          </>
        )}

        {step === 'selfie' && (
          <>
            <p className="text-sm text-[#7088A8]">Take a clear <strong className="text-[#DCE5F2]">selfie</strong> holding your ID document</p>
            <div className="p-3 bg-[#E8922A]/10 border border-[#E8922A]/20 rounded-xl text-xs text-[#DCE5F2]">
              • Good lighting, face clearly visible<br />
              • Hold ID next to your face<br />
              • Remove glasses if possible
            </div>
            <DropZone label="Upload selfie with ID" onFile={(f) => setFile('selfie', f)} preview={previews.selfie ?? null} />
            <div className="flex gap-3">
              <button onClick={() => setStep('id_back')} className="flex-1 border border-white/10 text-[#7088A8] py-2.5 rounded-lg hover:border-white/20">Back</button>
              <button onClick={() => selfie && setStep('review')} disabled={!selfie} className="flex-1 bg-[#E8922A] text-[#080C12] font-semibold py-2.5 rounded-lg hover:bg-[#F5B050] transition-colors disabled:opacity-50">Review</button>
            </div>
          </>
        )}

        {step === 'review' && (
          <>
            <p className="text-sm text-[#7088A8] mb-4">Review your documents before submitting</p>
            <div className="grid grid-cols-3 gap-3 mb-5">
              {previews.front && (
                <div>
                  <p className="text-xs text-[#7088A8] mb-1">ID Front</p>
                  // eslint-disable-next-line @next/next/no-img-element
                  <img src={previews.front} alt="ID front" className="w-full h-24 object-cover rounded-lg" />
                </div>
              )}
              {previews.back && (
                <div>
                  <p className="text-xs text-[#7088A8] mb-1">ID Back</p>
                  // eslint-disable-next-line @next/next/no-img-element
                  <img src={previews.back} alt="ID back" className="w-full h-24 object-cover rounded-lg" />
                </div>
              )}
              {previews.selfie && (
                <div>
                  <p className="text-xs text-[#7088A8] mb-1">Selfie</p>
                  // eslint-disable-next-line @next/next/no-img-element
                  <img src={previews.selfie} alt="Selfie" className="w-full h-24 object-cover rounded-lg" />
                </div>
              )}
            </div>
            <div className="flex gap-3">
              <button onClick={() => setStep('selfie')} className="flex-1 border border-white/10 text-[#7088A8] py-2.5 rounded-lg hover:border-white/20">Back</button>
              <button onClick={handleSubmit} disabled={loading} className="flex-1 bg-[#E8922A] text-[#080C12] font-semibold py-2.5 rounded-lg hover:bg-[#F5B050] transition-colors disabled:opacity-50">
                {loading ? 'Submitting...' : 'Submit KYC'}
              </button>
            </div>
          </>
        )}
      </div>
    </div>
  )
}
