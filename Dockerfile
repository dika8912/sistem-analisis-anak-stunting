FROM python:3.11-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Hugging Face Spaces default port is 7860, Koyeb uses 8000
ENV PORT=7860
EXPOSE 7860

CMD ["sh", "-c", "uvicorn stunting_app.main:app --host 0.0.0.0 --port ${PORT}"]
