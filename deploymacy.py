import sys
import time

def sleep_dots(sleep_time, dots=100):
    for i in range(dots):
        time.sleep(sleep_time/(dots+1))
        sys.stdout.write(".")
        sys.stdout.flush()
    time.sleep(sleep_time/(dots+1))
    print



if sys.argv[1] == "build":
    print "Build to ./build/ successful"
elif sys.argv[1] == "ami-create":
    print "Machine image created. Saved to " + sys.argv[2]
elif sys.argv[1] == "ami-boot":
    print "Connecting to EC2"
    sleep_dots(0.5)
    print "Transferring machine image"
    sleep_dots(1.0)
    print "Booting machine"
    sleep_dots(2.0)
    print "Deployment successful: 127.0.0.1"
elif sys.argv[1] == "ami-test":
    print "Testing " + sys.argv[2]
    sleep_dots(4.0)
    print "All tests successful"
